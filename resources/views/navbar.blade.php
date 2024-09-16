<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Storage Sync</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link @if (\Route::current()->getName() == 'file.index') active @endif" aria-current="page"
                    href="{{ route('file.index') }}">File</a>
            </div>
            <div class="navbar-nav">
                <a class="nav-link @if (\Route::current()->getName() == 'sync.index') active @endif" aria-current="page"
                    href="{{ route('sync.index') }}">Sync</a>
            </div>
            <div class="navbar-nav">
                <a class="nav-link @if (\Route::current()->getName() == 'setting.index') active @endif" aria-current="page"
                    href="{{ route('setting.index') }}">Setting</a>
            </div>
        </div>
    </div>
</nav>
