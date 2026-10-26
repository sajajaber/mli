<?php

namespace Tests\Feature;

use App\Filament\Resources\HeroAdvertisements\Pages\ListHeroAdvertisements;
use App\Http\Controllers\HomeController;
use App\Models\HeroAdvertisement;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HeroAdvertisementOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reorder_active_hero_images_and_public_homepage_uses_that_order(): void
    {
        $this->actingAs(UserFactory::new()->admin()->create());

        $first = $this->makeHero('first', 1);
        $second = $this->makeHero('second', 2);
        $third = $this->makeHero('third', 3);
        $inactive = $this->makeHero('inactive', 4, false);

        Livewire::test(ListHeroAdvertisements::class)
            ->call('saveHeroOrder', [
                $third->id,
                $first->id,
                $second->id,
            ])
            ->assertHasNoErrors();

        $expectedIds = [
            $third->id,
            $first->id,
            $second->id,
        ];

        $this->assertSame(
            $expectedIds,
            HeroAdvertisement::active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all(),
        );

        $homepageData = app(HomeController::class)->index()->getData();

        $this->assertSame(
            $expectedIds,
            $homepageData['heroAdvertisements']
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all(),
        );

        // Reordering active cards must leave inactive advertisements untouched.
        $this->assertSame(4, (int) $inactive->fresh()->sort_order);
    }

    public function test_activating_a_hero_image_adds_it_to_the_end_of_the_public_sequence(): void
    {
        $this->actingAs(UserFactory::new()->admin()->create());

        $first = $this->makeHero('first', 1);
        $second = $this->makeHero('second', 2);
        $inactive = $this->makeHero('inactive', 1, false);

        Livewire::test(ListHeroAdvertisements::class)
            ->call('toggleHeroActive', $inactive->id)
            ->assertHasNoErrors();

        $this->assertSame(
            3,
            (int) $inactive->fresh()->sort_order,
        );

        $homepageData = app(HomeController::class)->index()->getData();

        $this->assertSame(
            [$first->id, $second->id, $inactive->id],
            $homepageData['heroAdvertisements']
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all(),
        );
    }

    private function makeHero(string $name, int $sortOrder, bool $active = true): HeroAdvertisement
    {
        return HeroAdvertisement::query()->create([
            'image_path' => "hero/{$name}.jpg",
            'image_alt' => ucfirst($name),
            'sort_order' => $sortOrder,
            'is_active' => $active,
        ]);
    }
}
