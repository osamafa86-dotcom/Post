<?php

namespace App\Livewire\Main\Maktoob;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Plan;
use App\Models\SpecialPage;
use App\Services\Payment\Contract\PaymentGatewayContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Support extends Component
{
    public $indian_plans;
    public $international_plans;
    public $selectedPlanId;
    public $bottom_advert;
    public $paymentData = null;

    protected $paymentService;

    public function boot(PaymentGatewayContract $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Initialize component and load plans
     *
     * @param string|null $redirect_to_plan
     * @return void
     */
    public function mount($redirect_to_plan = null)
    {
        $this->indian_plans = Plan::where('type', 'india')->get();
        $this->international_plans = Plan::where('type', 'international')->get();

        // If we have a redirect_to_plan parameter and user is authenticated,
        // automatically trigger subscription for that plan
        if ($redirect_to_plan && auth('web_user')->check()) {
            // Use defer to ensure component is fully loaded before triggering subscription
            $this->dispatch('createSubscriptionAfterLogin', $redirect_to_plan);
        }
        $this->bottom_advert = Advertisement::query()->where('place' , AdvertisementPlaceEnum::MAIN_ADS->value)
            ->latest()->first();
    }

    /**
     * Create a subscription for the selected plan
     *
     * @param string $planId
     * @return void
     */
    public function createSubscription($planId)
    {
        // Check if user is authenticated
        if (!auth('web_user')->check()) {
            // Store the plan ID in the session to retrieve after login
            session()->put('selected_subscription_plan_id', $planId);

            // Redirect to the Maktoob login page
            return redirect()->route('main.maktoob.user_dashboard_login');
        }

        // User is authenticated, proceed with subscription
        $this->selectedPlanId = $planId;

        // Get the authenticated user ID
        $userId = auth('web_user')->id();

        $this->paymentData = $this->paymentService->createSubscription($planId, $userId);

        $this->dispatch('subscriptionCreated', $this->paymentData);
    }

    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.support');
    }
}
