<?php

namespace App\Observers;

use App\Models\Menu;

class MenuObserver
{
    /**
     * Handle the Menu "saved" event (covers both created and updated).
     */
    public function saved(Menu $menu): void
    {
        $this->flushCache($menu);
    }

    /**
     * Handle the Menu "deleted" event.
     */
    public function deleted(Menu $menu): void
    {
        $this->flushCache($menu);
    }

    /**
     * Flush cached sidebar menus for all affected app identifiers.
     */
    private function flushCache(Menu $menu): void
    {
        $originalApps = $menu->getOriginal('app');
        if (is_string($originalApps)) {
            $decoded = json_decode($originalApps, true);
            $originalApps = is_array($decoded) ? $decoded : [$originalApps];
        }

        $affectedApps = array_merge(
            is_array($originalApps) ? $originalApps : [],
            is_array($menu->app) ? $menu->app : []
        );

        Menu::flushSidebarCache($affectedApps);
    }
}
