<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Storage Sync</title>
</head>

<body>
    @include('navbar')

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-9">
                Folder : {{ $folder }}
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary float-end" id="syncbutton" onclick="sync()">Sync</button>
            </div>
            <div class="col-md-12 mt-2">
                <h3>Queue</h3>
                <div class="overflow-auto border" style="max-height: 60dvh">
                    <table class="table table-sm m-0 table-striped">
                        @if (count($files) > 0)
                            @foreach ($files as $file)
                                @php
                                    //getonly a-z, A-Z, 0-9
                                    $fileId = preg_replace('/[^A-Za-z0-9\-]/', '', $file);
                                @endphp
                                <tr id="{{ $fileId }}">
                                    <td width="3%">{{ $loop->iteration }}.</td>
                                    <td>{{ str_replace($folder, '/', $file) }}</td>
                                    <td style="width: 90px;">
                                        <div class="loading" style="display: none;"></div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center">No files to sync</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        var files = @json($files);

        $(document).ready(function() {
            var count = 0;

            setInterval(() => {
                count++;

                //make dots as count
                var dots = '';
                for (let i = 0; i < count; i++) {
                    dots += '.';
                }

                $('.loading').html('<span class="badge bg-warning">Loading ' + dots + '</span>');

                if (count == 3) {
                    count = -1;
                }
            }, 1000);
        });

        var countSync = 0;

        async function sync() {
            if (files.length == 0) {
                alert('No files to sync');
                return;
            }

            $('.loading').show();

            var lengthf = files.length;

            $('#syncbutton').attr('disabled', true);

            for (let i = 0; i < files.length; i++) {
                await syncFile(files[i]);
            }
        }

        function syncFile(file) {
            //getonly a-z, A-Z, 0-9
            var fileId = file.replace(/[^A-Za-z0-9\-]/g, '');
            //remove badge danger if exists
            $('#' + fileId).find('.badge.bg-danger').remove();
            //remove badge success if exists
            $('#' + fileId).find('.badge.bg-success').remove();

            $.ajax({
                url: '{{ route('sync.sync') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    file: file
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('#' + fileId).find('.loading').hide().after(
                            '<span class="badge bg-success">Success</span>');

                        // setTimeout(() => {
                        //     //remove row after 3 seconds
                        //     $('#' + fileId).remove();
                        // }, 3000);
                        countSync++;

                        if (countSync == files.length) {
                            countSync = 0;
                            $('#syncbutton').attr('disabled', false);
                        }
                    } else {
                        $('#' + fileId).find('.loading').hide().after(
                            '<span class="badge bg-danger">' + response.message + '</span>');
                    }
                },
                error: function(response) {
                    $('#' + fileId).find('.loading').hide().after(
                        '<span class="badge bg-danger">Failed</span>');

                }
            });
        }
    </script>
</body>

</html>
