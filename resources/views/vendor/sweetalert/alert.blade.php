@if (config('sweetalert.alwaysLoadJS') === true || Session::has('alert.config') || Session::has('alert.delete'))
    @if (config('sweetalert.animation.enable'))
        <link rel="stylesheet" href="{{ config('sweetalert.animatecss') }}">
    @endif

    @if (config('sweetalert.theme') != 'default')
        <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-{{ config('sweetalert.theme') }}" rel="stylesheet">
    @endif

    @if (config('sweetalert.neverLoadJS') === false)
        <script src="{{ $cdn ?? asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    @endif

    @if (Session::has('alert.delete') || Session::has('alert.config'))
        <script>
            document.addEventListener('click', function(event) {
                // Check if the clicked element or its parent has the attribute
                var target = event.target;
                var confirmDeleteElement = target.closest('[data-confirm-delete]');

                if (confirmDeleteElement) {
                    event.preventDefault();
                    var delConfig = {!! Session::pull('alert.delete') !!};
                    var isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
                    if (isDark && delConfig) {
                        delConfig.background = '#1e293b';
                        delConfig.color = '#f8fafc';
                        if (!delConfig.customClass) delConfig.customClass = {};
                        delConfig.customClass.popup = (delConfig.customClass.popup || '') + ' border border-slate-700 shadow-2xl rounded-3xl';
                    }
                    Swal.fire(delConfig).then(function(result) {
                        if (result.isConfirmed) {
                            var form = document.createElement('form');
                            form.action = confirmDeleteElement.href;
                            form.method = 'POST';
                            form.innerHTML = `
                            @csrf
                            @method('DELETE')
                        `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            });

            @if (Session::has('alert.config'))
                (function() {
                    var alertConfig = {!! Session::pull('alert.config') !!};
                    var isDark = document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark';
                    if (isDark && alertConfig) {
                        alertConfig.background = '#1e293b';
                        alertConfig.color = '#f8fafc';
                        if (!alertConfig.customClass) alertConfig.customClass = {};
                        alertConfig.customClass.popup = (alertConfig.customClass.popup || '') + ' border border-slate-700 shadow-2xl rounded-3xl';
                    }
                    Swal.fire(alertConfig);
                })();
            @endif
        </script>
    @endif
@endif
