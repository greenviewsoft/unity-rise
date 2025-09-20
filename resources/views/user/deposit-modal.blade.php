<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="depositModalLabel">{{ __('lang.deposit') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('user/deposite-address') }}" method="get">
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">{{ __('lang.amount') }}</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="method" class="form-label">{{ __('lang.payment_method') }}</label>
                        <select class="form-select" id="method" name="method" required>
                            <option value="">Select Payment Method</option>
                            <option value="usdt">USDT</option>
                            <option value="btc">Bitcoin</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">{{ __('lang.proceed') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>