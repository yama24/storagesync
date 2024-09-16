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

    <form action="{{ route('setting.store') }}" method="POST">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-9">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary float-end">Submit</button>
                </div>
                <div class="col-md-6 mt-2">
                    <h3>Settings</h3>
                    @csrf
                    <div class="mb-3">
                        <label for="project_id"
                            class="form-label
                            @error('gcs_project_id') text-danger @enderror">GCS
                            Project ID</label>

                        <input type="text" class="form-control @error('gcs_project_id') is-invalid @enderror"
                            name="gcs_project_id" value="{{ $setting['gcs_project_id'] ?? '' }}">
                        @error('gcs_project_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="bucket"
                            class="form-label
                            @error('gcs_bucket') text-danger @enderror">GCS
                            Bucket</label>

                        <input type="text" class="form-control @error('gcs_bucket') is-invalid @enderror"
                            name="gcs_bucket" value="{{ $setting['gcs_bucket'] ?? '' }}">
                        @error('gcs_bucket')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="folder"
                            class="form-label
                            @error('gcs_folder') text-danger @enderror">GCS
                            Folder</label>

                        <input type="text" class="form-control @error('gcs_folder') is-invalid @enderror"
                            name="gcs_folder" value="{{ $setting['gcs_folder'] ?? '' }}">
                        @error('gcs_folder')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="credentials"
                            class="form-label
                            @error('gcs_credentials') text-danger @enderror">GCS
                            Credential</label>
                        <textarea class="form-control @error('gcs_credentials') is-invalid @enderror" name="gcs_credentials" id="credentials"
                            rows="10">{{ old('gcs_credentials') ?? ($setting['gcs_credentials'] ?? '') }}</textarea>

                        @error('gcs_credentials')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 mt-2">
                    <h3>&nbsp;</h3>
                    <input type="hidden" name="fullpath" id="fullpath" value="{{ $setting['fullpath'] ?? '' }}">
                    <label class="form-label">Local Folder</label>
                    <div class="d-flex overflow-auto border" style="max-height: 70dvh;" id="foldertree">
                        <div class="lvlcontainer lv1 overflow-auto" style="min-width: 300px;" data-level="1">
                            <ul class="list-group">
                                @foreach ($folders as $folder)
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="lv1"
                                                data-val="{{ $folder }}"
                                                onclick="selectFolder('1', '{{ $folder }}')"
                                                id="lv1-{{ $folder }}">
                                            <label class="form-check-label w-100" for="lv1-{{ $folder }}">
                                                {{ $folder }}
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @include('script')
    <script>
        $(document).ready(function() {
            var fullpath = $('#fullpath').val();
            if (fullpath) {
                var folders = fullpath.split('/');
                //remove 3 first element
                folders = folders.slice(3);
                var lvl = 1;
                // folders.forEach(folder => {
                //     // $('#foldertree .lv' + lvl + ' input[data-val="' + folder + '"]').prop('checked', true)
                //     //     .trigger('click');
                //     lvl++;
                // });
                var intervalFolder = setInterval(function() {
                    if (folders[lvl - 1].length > 0) {
                        $('#foldertree .lv' + lvl + ' input[data-val="' + folders[lvl - 1] + '"]').prop(
                            'checked',
                            true)
                        // .trigger('click');
                        selectFolder(lvl, folders[lvl - 1]);
                        lvl++;
                    } else {
                        clearInterval(intervalFolder);
                    }
                }, 500);
            }
        });

        var folderPath = {};

        function selectFolder(lvl, folder) {
            //check if element selected
            if ($('.lv' + lvl + ' input:checked').length > 0) {
                folderPath[lvl] = folder;

                folderStr = '';
                for (let i = 1; i <= lvl; i++) {
                    folderStr += folderPath[i] + '%2F';
                }

                //remove next level
                let allnextlvl = $('#foldertree .lvlcontainer');
                allnextlvl.each(function(index, element) {
                    if ($(element).data('level') > lvl) {
                        $(element).remove();
                    }
                });

                let fullpath = '{{ $home }}' + '/' + folderStr.replaceAll('%2F', '/');
                $('#fullpath').val(fullpath);

                $.ajax({
                    url: '{{ route('setting.folder') }}' + '?folder=' + folderStr,
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            let folders = response.folders;
                            if (folders.length > 0) {
                                let newlvl = parseInt(lvl) + 1;
                                let newlvlcontainer = $('#foldertree .lv' + lvl).clone();
                                //remove class
                                newlvlcontainer.removeClass('lv' + lvl);
                                newlvlcontainer.addClass('lv' + newlvl);
                                newlvlcontainer.attr('data-level', newlvl);
                                newlvlcontainer.find('.list-group').empty();
                                folders.forEach(folder => {
                                    newlvlcontainer.find('.list-group').append(
                                        '<li class="list-group-item"><div class="form-check"><input class="form-check-input" type="radio" name="lv' +
                                        newlvl + '" data-val="' +
                                        folder + '" onclick="selectFolder(\'' +
                                        newlvl + '\', \'' +
                                        folder + '\')" id="lv' + newlvl + '-' + folder +
                                        '"><label class="form-check-label w-100" for="lv' + newlvl +
                                        '-' + folder + '">' + folder + '</label></div></li>');
                                });
                                $('#foldertree').append(newlvlcontainer);
                            }
                        }
                    }
                });
            }
        }
    </script>
</body>

</html>
