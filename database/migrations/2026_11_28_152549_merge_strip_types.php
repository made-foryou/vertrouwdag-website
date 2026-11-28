<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Moves the strips of the five strip types that were merged into another (2026-11-28) to that type, with the
 * variant that gives their markup: hero-diensten and hero-over-mij into hero, hero-archief into hero-blog,
 * artikel-citaat into citaat and kennismaking into cta.
 *
 * Only the type of such a strip changes, and its values get the key variant. Its id, its visibility and all its
 * other values stay as they were: every one of them is a field of the new type, and none of the five had a list,
 * whose items the new type would refuse with keys it does not know. That holds wherever the cms keeps whole strips:
 * the published and the draft strips of every page, also a deleted one that can still be restored, and the strips
 * and removed strips of every version. The media usages of a moved strip follow its type; the field paths stay.
 *
 * It writes through the query builder, so no model events fire and no page looks edited (updated_at). It only
 * touches what still has an old type, so it can run again.
 */
return new class extends Migration
{
    /**
     * @var array<string, array{type: string, variant: string}>
     */
    private const array MERGED = [
        'hero-diensten' => ['type' => 'hero', 'variant' => 'diensten'],
        'hero-over-mij' => ['type' => 'hero', 'variant' => 'over-mij'],
        'hero-archief' => ['type' => 'hero-blog', 'variant' => 'archief'],
        'artikel-citaat' => ['type' => 'citaat', 'variant' => 'artikel'],
        'kennismaking' => ['type' => 'cta', 'variant' => 'kaart'],
    ];

    /**
     * The old and new type of every strip this run moved, by strip id, for its media usages.
     *
     * @var array<string, array{from: string, to: string}>
     */
    private array $moved = [];

    public function up(): void
    {
        DB::transaction(fn () => $this->rewrite($this->merge(...)));
    }

    /**
     * Puts the strips back under their old type, by their variant, and takes the variant away. A strip an editor
     * made later with one of these variants goes back as well: the old code only knows it under the old type.
     */
    public function down(): void
    {
        DB::transaction(fn () => $this->rewrite($this->split(...)));
    }

    /**
     * @param  Closure(object): bool  $change  changes one stored strip in place, and says whether it did
     */
    private function rewrite(Closure $change): void
    {
        $this->moved = [];

        foreach (DB::table('cms_pages')->select(['id', 'strips', 'draft_strips'])->orderBy('id')->get() as $page) {
            $this->update('cms_pages', $page->id, [
                'strips' => $this->changeList($page->strips, $change),
                'draft_strips' => $this->changeList($page->draft_strips, $change),
            ]);
        }

        foreach (DB::table('cms_page_versions')->select(['id', 'strips', 'changes'])->orderBy('id')->get() as $version) {
            $this->update('cms_page_versions', $version->id, [
                'strips' => $this->changeList($version->strips, $change),
                'changes' => $this->changeRemoved($version->changes, $change),
            ]);
        }

        foreach ($this->moved as $stripId => ['from' => $from, 'to' => $to]) {
            DB::table('cms_media_usages')->where('strip_id', $stripId)->where('strip_type', $from)->update(['strip_type' => $to]);
        }
    }

    /**
     * @param  array<string, ?string>  $columns  the new json per column, or null where nothing changed
     */
    private function update(string $table, int $id, array $columns): void
    {
        $columns = array_filter($columns, fn (?string $json): bool => $json !== null);

        if ($columns !== []) {
            DB::table($table)->where('id', $id)->update($columns);
        }
    }

    /**
     * The stored list of strips with every strip changed, as json, or null when no strip changed. Decoded as
     * objects, so an empty object stays an object.
     *
     * @param  Closure(object): bool  $change
     */
    private function changeList(?string $json, Closure $change): ?string
    {
        $strips = $json === null ? null : json_decode($json);

        if (! is_array($strips)) {
            return null;
        }

        $changed = false;

        foreach ($strips as $strip) {
            $changed = (is_object($strip) && $change($strip)) || $changed;
        }

        return $changed ? json_encode($strips) : null;
    }

    /**
     * The stored changes of a version with every removed strip changed, as json, or null when none changed.
     *
     * @param  Closure(object): bool  $change
     */
    private function changeRemoved(?string $json, Closure $change): ?string
    {
        $changes = $json === null ? null : json_decode($json);

        if (! is_object($changes) || ! isset($changes->removed)) {
            return null;
        }

        $removed = $this->changeList(json_encode($changes->removed), $change);

        if ($removed === null) {
            return null;
        }

        $changes->removed = json_decode($removed);

        return json_encode($changes);
    }

    private function merge(object $strip): bool
    {
        $target = self::MERGED[$strip->type ?? ''] ?? null;

        if ($target === null) {
            return false;
        }

        $this->moved[(string) ($strip->id ?? '')] = ['from' => $strip->type, 'to' => $target['type']];
        $strip->type = $target['type'];
        $strip->values = (object) [...$this->values($strip), 'variant' => $target['variant']];

        return true;
    }

    private function split(object $strip): bool
    {
        $values = $this->values($strip);

        foreach (self::MERGED as $old => $target) {
            if (($strip->type ?? '') === $target['type'] && ($values['variant'] ?? null) === $target['variant']) {
                unset($values['variant']);
                $this->moved[(string) ($strip->id ?? '')] = ['from' => $strip->type, 'to' => $old];
                $strip->type = $old;
                $strip->values = (object) $values;

                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function values(object $strip): array
    {
        $values = $strip->values ?? null;

        return is_object($values) || is_array($values) ? (array) $values : [];
    }
};
