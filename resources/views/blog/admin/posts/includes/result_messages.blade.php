@php /** @var \Illunimate\Support\MessageBag $errors */ @endphp
        @if ($errors->any())
            <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <ul>
                            @foreach ($errors->all() as $errorTxt) {{-- виводимо всі помилки --}}
                                <li>{{ $errorTxt }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        @if (session('success'))
            <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session()->get('success') }}
                    </div>
                </div>
            </div>
        @endif