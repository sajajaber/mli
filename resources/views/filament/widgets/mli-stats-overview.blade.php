@php
    $metrics = $this->getDashboardMetrics();

    $nextScheduled = $metrics['next_scheduled']
        ? \Illuminate\Support\Carbon::parse($metrics['next_scheduled'])
        : null;
@endphp

<div class="mli-dashboard">
    <div class="mli-dashboard__intro">
        <div>
            <span class="mli-dashboard__eyebrow">MEDIA LINK INTERNATIONAL</span>
            <h2>Good evening. Here’s your content overview.</h2>
            <p>Keep an eye on what is live, what needs attention, and what is coming next.</p>
        </div>
        <div class="mli-dashboard__mark">MLI</div>
    </div>

    <div class="mli-dashboard__hero-grid">
        <div class="mli-dashboard-card mli-dashboard-card--primary">
            <div class="mli-dashboard-card__top">
                <span>Live on the site</span>
                <span class="mli-dashboard-card__dot"></span>
            </div>
            <strong>{{ $metrics['live_count'] }}</strong>
            <small>Published shows and news</small>
        </div>

        <div class="mli-dashboard-card mli-dashboard-card--attention">
            <div class="mli-dashboard-card__top">
                <span>Needs attention</span>
                <span class="mli-dashboard-card__icon">!</span>
            </div>
            <strong>{{ $metrics['draft_count'] }}</strong>
            <small>{{ $metrics['draft_count'] ? 'Drafts waiting to be finished or published' : 'Everything is up to date' }}</small>
        </div>

        <div class="mli-dashboard-card mli-dashboard-card--schedule">
            <div class="mli-dashboard-card__top">
                <span>Publishing soon</span>
                <span class="mli-dashboard-card__icon">→</span>
            </div>
            <strong>{{ $metrics['scheduled_count'] }}</strong>
            <small>
                @if ($nextScheduled)
                    Next: {{ $nextScheduled->format('M j, g:ia') }}
                @else
                    Nothing scheduled
                @endif
            </small>
        </div>
    </div>

    <div class="mli-dashboard__metrics">
        <div>
            <span>Added this week</span>
            <strong>{{ $metrics['added_this_week'] }}</strong>
        </div>
        <div>
            <span>Active team</span>
            <strong>{{ $metrics['active_team_members'] }}</strong>
        </div>
        <div>
            <span>Clients</span>
            <strong>{{ $metrics['clients'] }}</strong>
        </div>
    </div>
</div>
