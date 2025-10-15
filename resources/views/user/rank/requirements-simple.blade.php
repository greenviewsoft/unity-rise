<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="rank-requirements-header">
                <div class="d-flex align-items-center mb-3">
                    <a href="{{ URL('user/dashboard') }}" class="btn btn-outline-light me-3">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <div>



                        <h4 class="text-white mb-2 fw-bold">
                            <i class="bi bi-bar-chart-steps me-2" style="color: var(--light-purple);"></i>
                            Rank Progress
                        </h4>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="rank-badge current">
                                <i class="bi bi-award-fill me-1"></i>
                                {{ $current_rank_name ?? 'Unranked' }}
                            </span>
                            @if ($next_rank_name)
                                <i class="bi bi-arrow-right" style="color: var(--light-purple);"></i>
                                <span class="rank-badge next">
                                    <i class="bi bi-arrow-up-circle me-1"></i>
                                    {{ $next_rank_name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Upgrade Button -->
                <div class="rank-upgrade-center-section mt-2">

                    {{-- Full Debug Info --}}
                    <div class="debug-info"
                        style="background: #1a1a1a; padding: 15px; border-radius: 10px; margin-bottom: 15px; font-family: monospace; font-size: 12px;">
                        <!-- ... existing debug code ... -->
                    </div>

                    {{-- Check if current rank completed, not next rank --}}
                    @if ($rank_unlock_ready)
                        <button id="claimUpgradeBtn" class="rank-upgrade-center-btn">
                            <i class="bi bi-trophy-fill"></i>
                            <span>Claim Rank Reward (${{ number_format($current_rank_reward, 2) }})</span>
                            <i class="bi bi-arrow-right-short ms-auto"></i>
                        </button>
                    @else
                        <div class="rank-upgrade-center-btn" style="opacity: 0.6; cursor: not-allowed;">
                            <i class="bi bi-trophy-fill"></i>
                            <span>Complete Requirements First</span>
                            <i class="bi bi-info-circle ms-auto"></i>
                        </div>
                    @endif
                </div>
            </div>



            <!-- Rank Progress Card -->
            <div class="rank-requirements-card">
                <div class="rank-header">
                    <div class="rank-info">
                        <h5 class="rank-title">{{ $current_rank_name ?? 'Unranked' }} Requirements</h5>
                        <p class="rank-subtitle">Your current rank requirements and progress</p>
                    </div>
                    <div class="rank-bonus">
                        <div class="bonus-amount">${{ number_format($current_rank_reward ?? 0, 2) }}</div>
                        <div class="bonus-label">RANK REWARD</div>
                    </div>
                </div>

                <!-- Requirements List -->
                <div class="requirements-list">
                    <!-- Personal Investment Requirement -->
                    <div class="requirement-item">
                        <div class="requirement-header">
                            <div class="requirement-icon personal-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Personal Investment</h6>
                                <p class="requirement-desc">Your total personal investment amount</p>
                            </div>
                            <div class="requirement-status">
                                @if (($personal_investment ?? 0) >= ($current_rank_personal_req ?? 0))
                                    <span class="status-badge completed">
                                        <i class="bi bi-check-circle-fill"></i> Completed
                                    </span>
                                @else
                                    <span class="status-badge pending">
                                        <i class="bi bi-clock-fill"></i> In Progress
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="requirement-progress">
                            <div class="progress-info">
                                <span
                                    class="current-value">${{ number_format($current_personal_progress ?? 0, 2) }}</span>
                                <span class="target-value">/
                                    ${{ number_format($required_personal_progress ?? 0, 2) }}</span>
                                <small class="text-muted">(Total:
                                    ${{ number_format($personal_investment ?? 0, 2) }})</small>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        style="width: {{ min(100, $personal_investment_progress ?? 0) }}%"></div>
                                </div>
                                <span
                                    class="progress-percent">{{ number_format($personal_investment_progress ?? 0, 1) }}%</span>
                            </div>
                            @if (($personal_investment_remaining ?? 0) > 0)
                                <div class="remaining-info">
                                    <i class="bi bi-info-circle"></i>
                                    <span>${{ number_format($personal_investment_remaining, 2) }} remaining to
                                        complete</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Direct Referrals Requirement -->
                    <div class="requirement-item">
                        <div class="requirement-header">
                            <div class="requirement-icon referral-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Direct Referrals</h6>
                                <p class="requirement-desc">Number of users you directly referred</p>
                            </div>
                            <div class="requirement-status">
                                @if (($direct_referrals ?? 0) >= ($current_rank_direct_req ?? 0))
                                    <span class="status-badge completed">
                                        <i class="bi bi-check-circle-fill"></i> Completed
                                    </span>
                                @else
                                    <span class="status-badge pending">
                                        <i class="bi bi-clock-fill"></i> In Progress
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="requirement-progress">
                            <div class="progress-info">
                                <span class="current-value">{{ $current_direct_progress ?? 0 }}</span>
                                <span class="target-value">/ {{ $required_direct_progress ?? 0 }}</span>
                                <small class="text-muted">(Total: {{ $direct_referrals ?? 0 }})</small>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        style="width: {{ min(100, $direct_referrals_progress ?? 0) }}%"></div>
                                </div>
                                <span
                                    class="progress-percent">{{ number_format($direct_referrals_progress ?? 0, 1) }}%</span>
                            </div>
                            @if (($direct_referrals_remaining ?? 0) > 0)
                                <div class="remaining-info">
                                    <i class="bi bi-info-circle"></i>
                                    <span>{{ $direct_referrals_remaining }} more referrals needed</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Team Investment Requirement -->
                    <div class="requirement-item">
                        <div class="requirement-header">
                            <div class="requirement-icon team-icon">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Team Investment</h6>
                                <p class="requirement-desc">Total investment from your entire team</p>
                            </div>
                            <div class="requirement-status">
                                @if (($team_investment ?? 0) >= ($current_rank_team_req ?? 0))
                                    <span class="status-badge completed">
                                        <i class="bi bi-check-circle-fill"></i> Completed
                                    </span>
                                @else
                                    <span class="status-badge pending">
                                        <i class="bi bi-clock-fill"></i> In Progress
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="requirement-progress">
                            <div class="progress-info">
                                <span
                                    class="current-value">${{ number_format($current_team_progress ?? 0, 2) }}</span>
                                <span class="target-value">/
                                    ${{ number_format($required_team_progress ?? 0, 2) }}</span>
                                <small class="text-muted">(Total:
                                    ${{ number_format($team_investment ?? 0, 2) }})</small>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        style="width: {{ min(100, $team_investment_progress ?? 0) }}%"></div>
                                </div>
                                <span
                                    class="progress-percent">{{ number_format($team_investment_progress ?? 0, 1) }}%</span>
                            </div>
                            @if (($team_investment_remaining ?? 0) > 0)
                                <div class="remaining-info">
                                    <i class="bi bi-info-circle"></i>
                                    <span>${{ number_format($team_investment_remaining, 2) }} remaining from
                                        team</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Section -->
                <div class="action-section">
                    @if ($rank_unlock_ready ?? false)
                        <div class="unlock-ready-section">
                            <div class="success-message">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Congratulations! You've completed all requirements for
                                    {{ $current_rank_name ?? 'your current rank' }}!</span>
                            </div>
                            @if ($next_rank_name)
                                <div class="next-rank-info mt-3">
                                    <p class="text-muted">Ready to work towards {{ $next_rank_name }} rank?</p>
                                    <a href="{{ route('user.rank.upgrade') }}" class="btn btn-primary btn-lg">
                                        <i class="bi bi-arrow-up-circle-fill"></i>
                                        View Next Rank Requirements
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="progress-summary">
                            <div class="summary-message">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>Complete all requirements above to maintain your
                                    {{ $current_rank_name ?? 'current rank' }} status!</span>
                            </div>
                            @if ($next_rank_name)
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Purple Dark Theme */
    :root {
        --primary-purple: #8b5cf6;
        --secondary-purple: #7c3aed;
        --dark-purple: #5b21b6;
        --light-purple: #a78bfa;
        --accent-purple: #c084fc;
        --bg-dark: #1e1b2e;
        --bg-card: #2a2640;
        --bg-item: #352f54;
        --text-primary: #f1f0fb;
        --text-secondary: #bfb9e4;
        --text-muted: #8b85a8;
    }



    .rank-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .rank-badge.current {
        background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
        color: #fff;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
    }

    .rank-badge.next {
        background: rgba(139, 92, 246, 0.15);
        color: var(--light-purple);
        border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .rank-badge:hover {
        transform: translateY(-2px);
    }


    /* Header Section */
    .rank-requirements-header {
        background: linear-gradient(135deg, var(--bg-dark) 0%, var(--dark-purple) 100%);
        padding: 25px;
        border-radius: 20px;
        margin-bottom: 25px;
        border: 1px solid rgba(139, 92, 246, 0.2);
        box-shadow: 0 8px 32px rgba(139, 92, 246, 0.15);
    }

    .rank-requirements-header h4 {
        color: var(--text-primary);
        font-weight: 700;
        text-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
    }

    .rank-requirements-header .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: var(--text-primary);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .rank-requirements-header .btn-outline-light:hover {
        background: rgba(139, 92, 246, 0.2);
        border-color: var(--primary-purple);
        transform: translateX(-3px);
    }

    /* Upgrade Button */
    .rank-upgrade-center-section {
        text-align: center;
        margin: 20px 0;
    }

    .rank-upgrade-center-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
        color: #fff;
        padding: 16px 32px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        width: 100%;
        max-width: 500px;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .rank-upgrade-center-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .rank-upgrade-center-btn:hover::before {
        left: 100%;
    }

    .rank-upgrade-center-btn:hover {
        background: linear-gradient(135deg, var(--secondary-purple) 0%, var(--dark-purple) 100%);
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(139, 92, 246, 0.6);
        color: #fff;
        text-decoration: none;
    }

    .rank-upgrade-center-btn .bi-arrow-right-short {
        position: absolute;
        right: 20px;
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }

    .rank-upgrade-center-btn:hover .bi-arrow-right-short {
        transform: translateX(5px);
    }

    /* Main Card */
    .rank-requirements-card {
        background: var(--bg-card);
        border-radius: 24px;
        padding: 35px;
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.15);
        backdrop-filter: blur(10px);
    }

    .rank-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 35px;
        padding-bottom: 25px;
        border-bottom: 2px solid rgba(139, 92, 246, 0.2);
    }

    .rank-title {
        color: var(--text-primary);
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 8px;
        text-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
    }

    .rank-subtitle {
        color: var(--text-secondary);
        font-size: 0.95rem;
        margin: 0;
    }

    .rank-bonus {
        text-align: right;
        background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
        padding: 20px 28px;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
    }

    .rank-bonus::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {

        0%,
        100% {
            transform: translate(0, 0) rotate(0deg);
        }

        50% {
            transform: translate(-20px, -20px) rotate(180deg);
        }
    }

    .bonus-amount {
        color: #fff;
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1;
        position: relative;
        z-index: 1;
    }

    .bonus-label {
        color: rgba(255, 255, 255, 0.95);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        margin-top: 5px;
        position: relative;
        z-index: 1;
    }

    /* Requirements List */
    .requirements-list {
        margin-bottom: 35px;
    }

    .requirement-item {
        background: var(--bg-item);
        border-radius: 20px;
        padding: 28px;
        margin-bottom: 24px;
        border: 1px solid rgba(139, 92, 246, 0.15);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .requirement-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--primary-purple) 0%, var(--secondary-purple) 100%);
        transform: scaleY(0);
        transition: transform 0.4s ease;
    }

    .requirement-item:hover {
        background: rgba(139, 92, 246, 0.08);
        transform: translateX(8px);
        border-color: var(--primary-purple);
        box-shadow: 0 8px 24px rgba(139, 92, 246, 0.2);
    }

    .requirement-item:hover::before {
        transform: scaleY(1);
    }

    .requirement-header {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
    }

    .requirement-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 18px;
        font-size: 1.4rem;
        color: #fff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .personal-icon {
        background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    }

    .referral-icon {
        background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
    }

    .team-icon {
        background: linear-gradient(135deg, #06b6d4 0%, #8b5cf6 100%);
    }

    .requirement-item:hover .requirement-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .requirement-info {
        flex: 1;
    }

    .requirement-title {
        color: var(--text-primary);
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .requirement-desc {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin: 0;
    }

    .status-badge {
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .status-badge.completed {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
    }

    .status-badge.pending {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
    }

    /* Progress Section */
    .requirement-progress {
        margin-left: 74px;
    }

    .progress-info {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        gap: 8px;
    }

    .current-value {
        color: var(--light-purple);
        font-size: 1.2rem;
        font-weight: 800;
    }

    .target-value {
        color: var(--text-secondary);
        font-size: 1.05rem;
        font-weight: 600;
    }

    .progress-info small {
        color: var(--text-muted);
        margin-left: 8px;
    }

    .progress-bar-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 12px;
    }

    .progress-bar {
        flex: 1;
        height: 12px;
        background: rgba(139, 92, 246, 0.1);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-purple) 0%, var(--accent-purple) 100%);
        border-radius: 20px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 12px rgba(139, 92, 246, 0.6);
        position: relative;
        overflow: hidden;
    }

    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: progress-shine 2s infinite;
    }

    @keyframes progress-shine {
        0% {
            left: -100%;
        }

        100% {
            left: 100%;
        }
    }

    .progress-percent {
        color: var(--light-purple);
        font-size: 0.95rem;
        font-weight: 700;
        min-width: 55px;
        text-align: right;
    }

    .remaining-info {
        color: var(--accent-purple);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    /* Action Section */
    .action-section {
        text-align: center;
        padding: 25px;
        background: rgba(139, 92, 246, 0.05);
        border-radius: 16px;
        border: 1px solid rgba(139, 92, 246, 0.1);
    }

    .unlock-ready-section .success-message {
        color: #10b981;
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .progress-summary .summary-message {
        color: var(--text-secondary);
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: inline-block;
        width: 22px;
        height: 22px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #ffffff;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .rank-header {
            flex-direction: column;
            gap: 20px;
        }

        .rank-bonus {
            width: 100%;
            text-align: center;
        }

        .requirement-progress {
            margin-left: 0;
            margin-top: 20px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const upgradeBtn = document.querySelector('#claimUpgradeBtn');
        let isProcessing = false;

        if (upgradeBtn) {
            upgradeBtn.addEventListener('click', function() {
                if (isProcessing) {
                    return;
                }
                isProcessing = true;

                upgradeBtn.disabled = true;
                const originalText = upgradeBtn.innerHTML;
                upgradeBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';

                fetch('{{ route('user.rank.upgrade') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(
                                `🎉 ${data.message}\n💰 Bonus: $${data.data.reward}\n🏆 Rank: ${data.data.rank_name}`);
                            window.location.reload();
                        } else {
                            alert(`❌ ${data.message}`);
                            upgradeBtn.disabled = false;
                            upgradeBtn.innerHTML = originalText;
                            isProcessing = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('❌ Error occurred');
                        upgradeBtn.disabled = false;
                        upgradeBtn.innerHTML = originalText;
                        isProcessing = false;
                    });
            });
        }
    });
</script>
