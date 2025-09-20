@foreach ($histories as $history)
    @php
        $product = App\Models\Product::find($history->product_id);
        $grab = App\Models\Product::find($history->grab_id);
    @endphp
    @if (isset($product) && isset($grab))
        <div class="product-histroy-grave-sc">
            <div class="card box-product">
                <div class="product-image-sc">
                    <div class="top-name-product">
                        <h4>{{ $product->title }}</h4>
                        <div class="badge status btn btn-success btn-xs">Completed</div>
                    </div>
                    <div class="product-image-title">
                        <div><img src="{{ asset('/' . $product->image) }}" alt="product1">
                        </div>
                        <div class="details">
                            <p>
                                {{ $product->description }}
                            </p>

                            <div class="product-price-info">
                                <span>${{ $product->price }}</span>
                                <span>x 7</span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="product-info-sc">
                    <div class="item">
                        <span>Order number</span>
                        <strong style="text-align: right;">#{{ $grab->id }}</strong>
                    </div>
                    <div class="item">
                        <span>Grab time</span>
                        @php
                            $grabtime = \Carbon\Carbon::parse($grab->created_at)->format('Y-m-d');
                        @endphp
                        <strong>{{ $grabtime }}</strong>
                    </div>
                    <div class="item">
                        <span>Commission fee</span>
                        <strong>${{ $history->amount }}</strong>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
