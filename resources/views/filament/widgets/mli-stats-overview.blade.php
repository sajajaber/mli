@php
    $data = $this->getDashboardData();
@endphp

<div class="mli-dashboard">
    <div class="mli-dashboard__intro">
        <div>
            <span class="mli-dashboard__eyebrow">MEDIA LINK INTERNATIONAL</span>
            <h2>Good evening.</h2>
            <p>Your content, at a glance.</p>
        </div>
        <div class="mli-dashboard__intro-mark">MLI</div>
    </div>

    <div class="mli-dashboard__status">
        <div class="mli-dashboard__status-item mli-dashboard__status-item--live">
            <div>
                <span class="mli-dashboard__label">Live</span>
                <small>Currently on the website</small>
            </div>
            <strong>{{ $data['live_count'] }}</strong>
        </div>

        <div class="mli-dashboard__status-item">
            <div>
                <span class="mli-dashboard__label">Drafts</span>
                <small>Waiting for attention</small>
            </div>
            <strong>{{ $data['draft_count'] }}</strong>
        </div>

        <div class="mli-dashboard__status-item">
            <div>
                <span class="mli-dashboard__label">Scheduled</span>
                <small>Ready to publish</small>
            </div>
            <strong>{{ $data['scheduled_count'] }}</strong>
        </div>
    </div>

    <div class="mli-dashboard__content">
        <section class="mli-dashboard__panel mli-dashboard__panel--content">
            <div class="mli-dashboard__panel-head">
                <div>
                    <span class="mli-dashboard__eyebrow">CONTENT</span>
                    <h3>Content at a glance</h3>
                </div>
                <span class="mli-dashboard__accent-line"></span>
            </div>

            <div class="mli-dashboard__content-list">
                <div class="mli-dashboard__content-row">
                    <span>Shows</span>
                    <strong>{{ $data['shows_count'] }}</strong>
                </div>
                <div class="mli-dashboard__content-row">
                    <span>News</span>
                    <strong>{{ $data['news_count'] }}</strong>
                </div>
                <div class="mli-dashboard__content-row">
                    <span>Active team</span>
                    <strong>{{ $data['active_team_members'] }}</strong>
                </div>
                <div class="mli-dashboard__content-row">
                    <span>Clients</span>
                    <strong>{{ $data['clients'] }}</strong>
                </div>
            </div>

            <div class="mli-dashboard__week">
                <span>Added this week</span>
                <strong>+{{ $data['added_this_week'] }}</strong>
            </div>
        </section>

        <section class="mli-dashboard__panel">
            <div class="mli-dashboard__panel-head">
                <div>
                    <span class="mli-dashboard__eyebrow">RECENT</span>
                    <h3>Latest activity</h3>
                </div>
            </div>

            <div class="mli-dashboard__activity-list">
                @forelse ($data['recent'] as $item)
                    <div class="mli-dashboard__activity">
                        <span class="mli-dashboard__activity-dot"></span>
                        <div class="mli-dashboard__activity-main">
                            <strong>{{ $item['title'] }}</strong>
                            <small>{{ $item['type'] }} · {{ ucfirst($item['status']) }}</small>
                        </div>
                        <time datetime="{{ \Illuminate\Support\Carbon::parse($item['created_at'])->toIso8601String() }}">
                            {{ \Illuminate\Support\Carbon::parse($item['created_at'])->diffForHumans() }}
                        </time>
                    </div>
                @empty
                    <p class="mli-dashboard__empty">No recent content yet.</p>
                @endforelse
            </div>
        </section>

        <section class="mli-dashboard__panel">
            <div class="mli-dashboard__panel-head">
                <div>
                    <span class="mli-dashboard__eyebrow">UPCOMING</span>
                    <h3>Publishing next</h3>
                </div>
            </div>

            <div class="mli-dashboard__upcoming-list">
                @forelse ($data['upcoming'] as $item)
                    <div class="mli-dashboard__upcoming">
                        <div>
                            <strong>{{ $item['title'] }}</strong>
                            <small>{{ $item['type'] }}</small>
                        </div>
                        <time datetime="{{ \Illuminate\Support\Carbon::parse($item['published_at'])->toIso8601String() }}">
                            {{ \Illuminate\Support\Carbon::parse($item['published_at'])->format('M j') }}<br>
                            <span>{{ \Illuminate\Support\Carbon::parse($item['published_at'])->format('g:i A') }}</span>
                        </time>
                    </div>
                @empty
                    <p class="mli-dashboard__empty">Nothing is scheduled.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
