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
    <style>
        .fileblock {
            cursor: pointer;
            color: black;
        }

        .fileblock:hover {
            background-color: #f0f0f0;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    @include('navbar')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-9">
            </div>
            <div class="col-md-3">
                {{-- <button type="button" class="btn btn-primary float-end" id="syncbutton" onclick="sync()">Sync</button> --}}
            </div>
            <div class="col-md-12 mt-2">
                <div class="d-flex align-items-center justify-content-between">
                    <h3>Files</h3>
                    {{-- <h6 id="path">/</h6> --}}
                    <div class="d-flex w-50">
                        <button class="btn btn-secondary btn-sm" onclick="backButton()" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Tooltip on top">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <input type="text" class="form-control form-control-sm" placeholder="Path" id="path"
                            value="/" onchange="refreshFile()">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9 pe-1">
                        <div class="overflow-auto border p-3 d-flex flex-wrap" id="file-container"
                            style="max-height: 70dvh">
                            @php
                                $iconLists = [
                                    'application/pdf' => [
                                        'icon' => 'file-pdf',
                                        'color' => 'danger',
                                        'viewable' => true,
                                    ],
                                    'image/jpeg' => [
                                        'icon' => 'file-image',
                                        'color' => 'primary',
                                        'viewable' => true,
                                    ],
                                    'image/png' => [
                                        'icon' => 'file-image',
                                        'color' => 'primary',
                                        'viewable' => true,
                                    ],
                                    'image/gif' => [
                                        'icon' => 'file-image',
                                        'color' => 'primary',
                                        'viewable' => true,
                                    ],
                                    'folder' => [
                                        'icon' => 'folder',
                                        'color' => 'warning',
                                        'viewable' => false,
                                    ],
                                    'audio/mpeg' => [
                                        'icon' => 'file-audio',
                                        'color' => 'info',
                                        'viewable' => true,
                                    ],
                                    'audio/ogg' => [
                                        'icon' => 'file-audio',
                                        'color' => 'info',
                                        'viewable' => true,
                                    ],
                                    'audio/wav' => [
                                        'icon' => 'file-audio',
                                        'color' => 'info',
                                        'viewable' => true,
                                    ],
                                    'audio/mp3' => [
                                        'icon' => 'file-audio',
                                        'color' => 'info',
                                        'viewable' => true,
                                    ],
                                    'audio/m4a' => [
                                        'icon' => 'file-audio',
                                        'color' => 'info',
                                        'viewable' => true,
                                    ],
                                    'audio/x-wav' => [
                                        'icon' => 'file-audio',
                                        'color' => 'info',
                                        'viewable' => true,
                                    ],
                                    'video/mp4' => [
                                        'icon' => 'file-video',
                                        'color' => 'success',
                                        'viewable' => true,
                                    ],
                                    'video/ogg' => [
                                        'icon' => 'file-video',
                                        'color' => 'success',
                                        'viewable' => true,
                                    ],
                                    'video/webm' => [
                                        'icon' => 'file-video',
                                        'color' => 'success',
                                        'viewable' => true,
                                    ],
                                    'text/plain' => [
                                        'icon' => 'file-alt',
                                        'color' => 'secondary',
                                        'viewable' => false,
                                    ],
                                    'application/zip' => [
                                        'icon' => 'file-archive',
                                        'color' => 'dark',
                                        'viewable' => false,
                                    ],
                                ];
                            @endphp
                            @foreach ($filetree['children'] ?? [] as $key => $file)
                                @php
                                    $type = $file['type'] !== 'folder' ? 'File' : 'Folder';
                                    $name = $file['name'];
                                    $icon = 'file-alt';
                                    $color = 'secondary';
                                    if (isset($iconLists[$file['type']])) {
                                        $icon = $iconLists[$file['type']]['icon'];
                                        $color = $iconLists[$file['type']]['color'];
                                    }
                                @endphp
                                <a href="javascript:;" class="m-2 fileblock" style="width: 8rem;"
                                    onclick="select{{ $type }}('{{ $name }}')">
                                    <center>
                                        <i class="text-{{ $color }} fas fa-{{ $icon }}"
                                            style="font-size: 5rem"></i><br>
                                        <small>
                                            <span class="mt-2 text-sm"
                                                style="word-wrap: break-word;">{{ $name }}</span>
                                        </small>
                                    </center>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-3 ps-1">
                        <div class="border p-3">
                            <h6>File Info : </h6>
                            <div id="fileInfo">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('script')
    <script>
        var fileTree = @json($filetree['children'] ?? []);

        var iconLists = @json($iconLists);

        $(document).ready(function() {});

        function selectFolder(folder) {
            var path = $('#path').val();
            if (path == '/') {
                path = '';
            }
            path += '/' + folder;
            //replace double slashes
            path = path.replace(/\/\//g, '/');
            $('#path').val(path).change();
        }

        function refreshFile() {
            var path = $('#path').val();

            var folders = path.split('/');

            var newFileTree = fileTree;
            for (var i = 1; i < folders.length; i++) {
                if (folders[i] == '') {
                    continue;
                }
                newFileTree = newFileTree[folders[i]];

                //check if children key exists
                if (newFileTree === undefined) {
                    let newPath = path.split('/').slice(0, i).join('/');
                    if (newPath == '') {
                        newPath = '/';
                    }

                    //replace double slashes
                    newPath = newPath.replace(/\/\//g, '/');

                    $('#path').val(newPath).change();
                    alert('Folder not found');
                    return;
                }

                newFileTree = newFileTree['children'];
            }

            var htmlFolder = '';
            var htmlFile = '';
            for (var key in newFileTree) {
                var file = newFileTree[key];
                var type = file['type'] !== 'folder' ? 'File' : 'Folder';
                var name = file['name'];
                // var selFolder = path + '/' + name;
                var selFolder = name;

                if (type == 'File') {
                    selFolder = JSON.stringify(file);
                    selFolder = encodeURI(selFolder);
                }

                var icon = 'file-alt';
                var color = 'secondary';
                if (iconLists[file['type']] !== undefined) {
                    icon = iconLists[file['type']]['icon'];
                    color = iconLists[file['type']]['color'];
                }

                html = '';
                html += '<a href="javascript:;" class="m-2 fileblock" style="width: 8rem;" onclick="select' + type + '(\'' +
                    selFolder + '\')">';
                html += '<center>';
                html += '<i class="text-' + color + ' fas fa-' + icon + '" style="font-size: 5rem"></i><br>';
                html += '<small>';
                html += '<span class="mt-2 text-sm" style="word-wrap: break-word;">' + name + '</span>';
                html += '</small>';
                html += '</center>';
                html += '</a>';
                if (type !== 'Folder') {
                    htmlFile += html;
                } else {
                    htmlFolder += html;
                }
            }

            $('#file-container').html(htmlFolder + htmlFile);
        }

        function selectFile(file) {
            file = JSON.parse(decodeURI(file));

            var viewable = false;
            if (iconLists[file['type']] !== undefined) {
                viewable = iconLists[file['type']]['viewable'];
            }

            $('#fileInfo').html('');
            var html = '';
            html += '<table class="table table-sm table-striped w-100">';
            //loop through file
            for (var key in file) {
                var value = file[key];
                var name = key;
                //ucfirst
                name = name.charAt(0).toUpperCase() + name.slice(1);

                if (name == 'Size') {
                    // value = (value / 1024).toFixed(2) + ' KB';
                    //convert to human readable size
                    var i = Math.floor(Math.log(value) / Math.log(1024));
                    value = (value / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i];
                }

                if (name == 'Path') {
                    continue;
                }

                if (name == 'Lastmodified') {
                    name = 'Last Modified';
                }

                html += '<tr>';
                html += '<td><b>' + name + ': </b>' + value + '</td>';
                html += '</tr>';
            }
            html += '</table>';
            html +=
                '<button class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Download" onclick="downloadFile(\'' +
                file['path'] + '\')"><i class="fas fa-download"></i></button>';
            if (viewable) {
                html +=
                    '<button class="btn btn-danger btn-sm ms-1" data-bs-toggle="tooltip" title="View" onclick="viewFile(\'' +
                    file['path'] + '\')"><i class="fas fa-eye"></i></button>';
            }
            $('#fileInfo').html(html);
            initTooltip();
        }

        function backButton() {
            var path = $('#path').val();
            var folders = path.split('/');
            folders.pop();
            var newPath = folders.join('/');
            if (newPath == '') {
                newPath = '/';
            }

            //replace double slashes
            newPath = newPath.replace(/\/\//g, '/');

            $('#path').val(newPath).change();
        }

        function downloadFile(path) {
            //ajax download file
            $.ajax({
                url: '{{ route('file.download') }}' + '?path=' + path,
                type: 'GET',
                success: function(response, status, xhr) {
                    //save file
                    var responseHeaders = xhr.getAllResponseHeaders();
                    var filename = responseHeaders.split('filename="')[1];
                    filename = filename.split('"')[0];
                    var blob = new Blob([response]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();

                    //remove link
                    link.remove();
                }
            });
        }

        function viewFile(path) {
            path = encodeURI(path);
            // open in new tab
            window.open('{{ route('file.view') }}' + '?path=' + path, '_blank');
        }
    </script>

    <script>
        function initTooltip() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }
        initTooltip();
    </script>
</body>

</html>
