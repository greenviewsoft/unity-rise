
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
                        <h4 class="text-white mb-0">Rank Progress</h4>
                        <p class="text-light mb-0">
                            Current Rank: <strong>{{ $current_rank_name ?? 'Unranked' }}</strong>
                            @if($next_rank_name)
                                → Next: <strong>{{ $next_rank_name }}</strong>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Rank Upgrade Center Button -->
                <div class="rank-upgrade-center-section mt-2">
                    @if($next_rank_unlock_ready)
                    <button id="claimUpgradeBtn" class="rank-upgrade-center-btn" style="border: none; background: none; width: 100%;">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Claim Rank Upgrade</span>
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
                            <div class="requirement-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="requirement-info">
                                <h6 class="requirement-title">Personal Investment</h6>
                                <p class="requirement-desc">Your total personal investment amount</p>
                            </div>
                            <div class="requirement-status">
                                @if(($personal_investment ?? 0) >= ($current_rank_personal_req ?? 0))
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
                                <span class="current-value">${{ number_format($current_personal_progress ?? 0, 2) }}</span>
                                <span class="target-value">/ ${{ number_format($required_personal_progress ?? 0, 2) }}</span>
                                <small class="text-muted">(Total: ${{ number_format($personal_investment ?? 0, 2) }})</small>
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
                                @if(($direct_referrals ?? 0) >= ($current_rank_direct_req ?? 0))
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
                                @if(($team_investment ?? 0) >= ($current_rank_team_req ?? 0))
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
                                <span class="current-value">${{ number_format($current_team_progress ?? 0, 2) }}</span>
                                <span class="target-value">/ ${{ number_format($required_team_progress ?? 0, 2) }}</span>
                                <small class="text-muted">(Total: ${{ number_format($team_investment ?? 0, 2) }})</small>
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
                            <span>Congratulations! You've completed all requirements for {{ $current_rank_name ?? 'your current rank' }}!</span>
                        </div>
                        @if($next_rank_name)
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
                            <span>Complete all requirements above to maintain your {{ $current_rank_name ?? 'current rank' }} status!</span>
                        </div>
                        @if($next_rank_name)
                        <div class="next-rank-info mt-3">
                            <p class="text-muted">Want to advance to {{ $next_rank_name }}?</p>
                            <a href="{{ route('user.rank.upgrade') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-up-circle"></i>
                                View Next Rank Requirements
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Rank Upgrade Center Button */
.rank-upgrade-center-section {
    text-align: center;
    margin: 15px 0;
}

.rank-upgrade-center-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    color: #fff;
    padding: 10px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 3px 12px rgba(139, 92, 246, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    cursor: pointer;
    width: 100%;
    justify-content: center;
    position: relative;
}

.rank-upgrade-center-btn:hover {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(139, 92, 246, 0.4);
    color: #fff;
    text-decoration: none;
}

.rank-upgrade-center-btn .bi-arrow-right-short {
    position: absolute;
    right: 15px;
    font-size: 1.2rem;
}

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

.unlock-rank-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const upgradeBtn = document.querySelector('#claimUpgradeBtn');
    
    if (upgradeBtn) {
        upgradeBtn.addEventListener('click', function() {
            // Disable button and show loading
            upgradeBtn.disabled = true;
            const originalText = upgradeBtn.innerHTML;
            upgradeBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';
            
            // Send AJAX request
            fetch('{{ route("user.rank.upgrade") }}', {
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
                    // Show success message
                    alert(`🎉 ${data.message}\n💰 Bonus Received: $${data.bonus_amount}\n🏆 New Rank: ${data.new_rank}`);
                    
                    // Redirect to refresh the page
                    window.location.href = data.redirect_url;
                } else {
                    // Show error message
                    alert(`❌ ${data.message}`);
                    
                    // Re-enable button
                    upgradeBtn.disabled = false;
                    upgradeBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ An error occurred while upgrading your rank. Please try again.');
                
                // Re-enable button
                upgradeBtn.disabled = false;
                upgradeBtn.innerHTML = originalText;
            });
        });
    }
});
</script>
