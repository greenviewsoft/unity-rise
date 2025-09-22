
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="rank-requirements-header">
                <div class="d-flex align-items-center mb-3">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-light me-3">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <h4 class="text-white mb-0">{{ $next_rank_name ?? 'Rank' }} Requirements</h4>
                </div>
            </div>

            @if($next_rank_name)
            <!-- Rank Progress Card -->
            <div class="rank-requirements-card">
                <div class="rank-header">
                    <div class="rank-info">
                        <h5 class="rank-title">{{ $next_rank_name }} Progress</h5>
                        <p class="rank-subtitle">Complete all requirements to unlock your next rank</p>
                    </div>
                    <div class="rank-bonus">
                        <div class="bonus-amount">${{ number_format($next_rank_reward ?? 0, 2) }}</div>
                        <div class="bonus-label">BONUS REWARD</div>
                    </div>
                </div>

                <!-- Requirements List -->
                <div class="requirements-list">
                    <!-- Personal Investment Requirement -->
                    <div class="requirement-item">
                        <div class="requirement-header">
                            <div class="requirement-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Personal Investment</h6>
                                <p class="requirement-desc">Your total personal investment amount</p>
                            </div>
                            <div class="requirement-status">
                                @if(($personal_investment ?? 0) >= ($next_rank_personal_req ?? 0))
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
                                <span class="current-value">${{ number_format($personal_investment ?? 0, 2) }}</span>
                                <span class="target-value">/ ${{ number_format($next_rank_personal_req ?? 0, 2) }}</span>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ min(100, ($personal_investment_progress ?? 0)) }}%"></div>
                                </div>
                                <span class="progress-percent">{{ number_format($personal_investment_progress ?? 0, 1) }}%</span>
                            </div>
                            @if(($personal_investment_remaining ?? 0) > 0)
                            <div class="remaining-info">
                                <i class="bi bi-info-circle"></i>
                                <span>${{ number_format($personal_investment_remaining, 2) }} remaining to complete</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Direct Referrals Requirement -->
                    <div class="requirement-item">
                        <div class="requirement-header">
                            <div class="requirement-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Direct Referrals</h6>
                                <p class="requirement-desc">Number of users you directly referred</p>
                            </div>
                            <div class="requirement-status">
                                @if(($direct_referrals ?? 0) >= ($next_rank_direct_req ?? 0))
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
                                <span class="current-value">{{ $direct_referrals ?? 0 }}</span>
                                <span class="target-value">/ {{ $next_rank_direct_req ?? 0 }}</span>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ min(100, ($direct_referrals_progress ?? 0)) }}%"></div>
                                </div>
                                <span class="progress-percent">{{ number_format($direct_referrals_progress ?? 0, 1) }}%</span>
                            </div>
                            @if(($direct_referrals_remaining ?? 0) > 0)
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
                            <div class="requirement-icon">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Team Investment</h6>
                                <p class="requirement-desc">Total investment from your entire team</p>
                            </div>
                            <div class="requirement-status">
                                @if(($team_investment ?? 0) >= ($next_rank_team_req ?? 0))
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
                                <span class="current-value">${{ number_format($team_investment ?? 0, 2) }}</span>
                                <span class="target-value">/ ${{ number_format($next_rank_team_req ?? 0, 2) }}</span>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ min(100, ($team_investment_progress ?? 0)) }}%"></div>
                                </div>
                                <span class="progress-percent">{{ number_format($team_investment_progress ?? 0, 1) }}%</span>
                            </div>
                            @if(($team_investment_remaining ?? 0) > 0)
                            <div class="remaining-info">
                                <i class="bi bi-info-circle"></i>
                                <span>${{ number_format($team_investment_remaining, 2) }} remaining from team</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Section -->
                <div class="action-section">
                    @if($rank_unlock_ready ?? false)
                    <div class="unlock-ready-section">
                        <div class="success-message">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Congratulations! You've completed all requirements for {{ $next_rank_name }}!</span>
                        </div>
                        <button class="btn btn-success btn-lg unlock-rank-btn">
                            <i class="bi bi-unlock-fill"></i>
                            Unlock {{ $next_rank_name }} Rank
                        </button>
                    </div>
                    @else
                    <div class="progress-summary">
                        <div class="summary-message">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>Complete all requirements above to unlock {{ $next_rank_name }} rank and earn your bonus!</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- Max Rank Achieved -->
            <div class="max-rank-card">
                <div class="max-rank-content">
                    <div class="crown-icon">
                        <i class="bi bi-crown-fill"></i>
                    </div>
                    <h3>Maximum Rank Achieved!</h3>
                    <p>Congratulations! You have reached the highest rank available.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.rank-requirements-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
}

.rank-requirements-card {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.rank-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.rank-title {
    color: #ecf0f1;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.rank-subtitle {
    color: #bdc3c7;
    font-size: 0.9rem;
    margin: 0;
}

.rank-bonus {
    text-align: right;
    background: linear-gradient(135deg, #f39c12, #e67e22);
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
}

.bonus-amount {
    color: #fff;
    font-size: 1.3rem;
    font-weight: 700;
    line-height: 1;
}

.bonus-label {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 1px;
    margin-top: 3px;
}

.requirements-list {
    margin-bottom: 30px;
}

.requirement-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.requirement-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.requirement-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.requirement-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1.2rem;
    color: #fff;
}

.requirement-info {
    flex: 1;
}

.requirement-title {
    color: #ecf0f1;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 3px;
}

.requirement-desc {
    color: #bdc3c7;
    font-size: 0.85rem;
    margin: 0;
}

.status-badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.status-badge.completed {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: #fff;
}

.status-badge.pending {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: #fff;
}

.requirement-progress {
    margin-left: 65px;
}

.progress-info {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.current-value {
    color: #3498db;
    font-size: 1.1rem;
    font-weight: 700;
}

.target-value {
    color: #bdc3c7;
    font-size: 1rem;
    font-weight: 600;
}

.progress-bar-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.progress-bar {
    flex: 1;
    height: 10px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3498db, #2ecc71);
    border-radius: 5px;
    transition: width 0.8s ease;
}

.progress-percent {
    color: #3498db;
    font-size: 0.9rem;
    font-weight: 600;
    min-width: 50px;
    text-align: right;
}

.remaining-info {
    color: #e67e22;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.action-section {
    text-align: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
}

.unlock-ready-section .success-message {
    color: #2ecc71;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.unlock-rank-btn {
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 25px;
}

.progress-summary .summary-message {
    color: #bdc3c7;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.max-rank-card {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border-radius: 20px;
    padding: 50px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(243, 156, 18, 0.3);
}

.crown-icon {
    font-size: 4rem;
    color: #fff;
    margin-bottom: 20px;
}

.max-rank-card h3 {
    color: #fff;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.max-rank-card p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    margin: 0;
}
</style>
