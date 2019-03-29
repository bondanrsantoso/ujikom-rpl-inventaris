@extends('layouts.app') 
@section('more-css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-html5-1.5.6/sc-2.0.0/datatables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.0/css/select.bootstrap4.min.css">
<style>
</style>
@endsection
 
@section('more-js-before-body')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-html5-1.5.6/b-print-1.5.6/sc-2.0.0/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
@endsection
 
@section('content') @component('components.sidebar', [ 'more_classes' => 'col-12 col-md-3 col-xl-2', 'active_link'
=> url('inventaris')]) @endcomponent
<div class="col-12 col-md-9 col-xl-10 pt-4">
    <h1>Daftar Inventaris</h1>

    <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#formModal">Tambah baru</button>

    <div class="modal" id="formModal">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-light">
                    <h5 class="modal-title">Inventaris</h5>
                    <button class="close" data-dismiss="modal" aria-label="close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="api/inventaris/add" method="POST" id="mainForm" autocomplete="off" novalidate>
                    <input type="hidden" name="api_token" value="{{$token}}">
                    <input type="hidden" name="id" id="idInventaris" value="new">
                    <div class="modal-body">
                        <div class="d-flex flex-column flex-md-row flex-wrap">
                            <div class="form-group col col-md-6 col-xl-4">
                                <label for="kode">Kode Inventaris</label>
                                <input type="text" name="kode" id="kode" class="form-control" required>
                            </div>
                            <div class="form-group col-12 col-md">
                                <label for="nama">Nama</label>
                                <input type="text" name="nama" id="nama" class="form-control" required>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label for="kondisi">Kondisi</label>
                                <select name="kondisi" id="kondisi" class="form-control" required>
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="tidak_ada">Tidak Ada</option>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" name="jumlah" id="jumlah" min="0" class="form-control" required>
                                <div class="invalid-feedback">Masukkan angka &gt; 0</div>
                            </div>
                            <div class="form-group col-12 col-md-8">
                                <label for="jenis">Jenis</label>
                                <select name="jenis" id="jenis" class="form-control" required>
                                    @foreach ($Jenis as $jenis)
                                    <option value="{{$jenis->id_jenis}}">{{$jenis->kode_jenis}} - {{$jenis->nama_jenis}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="ruang">Ruang</label>
                                <select name="ruang" id="ruang" class="form-control" required>
                                    @foreach ($Ruang as $ruang)
                                    <option value="{{$ruang->id_ruang}}">{{$ruang->kode_ruang}} - {{$ruang->nama_ruang}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="keterangan">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
                <form action="api/inventaris/delete" method="POST" id="deleteForm">
                    @method('DELETE')
                    <input type="hidden" name="api_token" value="{{$token}}">
                    <input type="hidden" name="id" id="idInventarisDelete" value="new">
                </form>
                <div class="modal-footer">
                    <button type="submit" form="mainForm" id="submit" class="btn btn-success">Tambah</button>
                    <button type="reset" form="mainForm" id="reset" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" form="deleteForm" id="delete" class="btn btn-danger d-none">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="successModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-light">
                    <h5 class="modal-title">Sukses</h3>
                </div>
                <div class="modal-body">
                    Operasi berhasil dilaksanakan
                </div>
                <div class="modal-footer">
                    <button class="btn-primary btn" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="confirmModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-light">
                    <h5 class="modal-title">Anda yakin?</h3>
                </div>
                <div class="modal-body">
                    Operasi yang akan dilakukan tidak bisa dibatalkan setelah ini
                </div>
                <div class="modal-footer">
                    <button class="btn-success btn confirm-button" data-dismiss="modal" data-confirm="true">Ya</button>
                    <button class="btn-warning btn confirm-button" data-dismiss="modal" data-confirm="false">Tidak</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="failedModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title">Gagal</h3>
                </div>
                <div class="modal-body">
                    Operasi gagal dilaksanakan
                </div>
                <div class="modal-footer">
                    <button class="btn-primary btn" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <table class="datatable table table-striped table-hover" id="inventaris-table">
        <thead class="thead-primary">
            <th>ID</th>
            <th>ID Jenis</th>
            <th>ID Ruang</th>
            <th>No.</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kondisi</th>
            <th>Jumlah</th>
            <th>Jenis</th>
            <th>Ruang</th>
            <th>Petugas</th>
            <th>Tanggal Register</th>
            <th>Keterangan</th>
        </thead>
    </table>
</div>
@endsection
 
@section('more-js-after-body')
<script>
    var $table;
    var isActionConfirmed = false;

    $(document).ready(function(){
        $table = $("#inventaris-table").DataTable({
            ordering: false,
            serverSide: true,
            ajax:'{{url("api/inventaris/get")}}',
            select: 'single',
            dom: 'fSrtip',
            scrollX: true,
            scrollY: (window.innerHeight > 550? window.innerHeight : 550 ) - 350,
            scrollCollapse: true,
            deferRender: true,
            scroller:true,
            columnDefs: [
                {
                    targets: [0],
                    visible:false
                },
                {
                    targets: [1],
                    visible:false
                },
                {
                    targets: [2],
                    visible:false
                },
            ],
            buttons: ['excel', 'pdf', 'print']
        });
        $table.on('select', function(e, dataTable, type, indexes){
            var selectedItem = $table.rows(indexes).data()[0];
            $("#idInventaris").val(selectedItem[0])
            $("#idInventarisDelete").val(selectedItem[0])
            $("#kode").val(selectedItem[4])
            $("#nama").val(selectedItem[5])
            $("#kondisi").val(selectedItem[6])
            $("#jumlah").val(selectedItem[7])
            $("#jenis").val(selectedItem[1])
            $("#ruang").val(selectedItem[2])
            $("#keterangan").val(selectedItem[12])
            $("#submit").html("Perbarui")
            $("#reset").addClass("d-none")
            $("#delete").removeClass("d-none")
            $("#formModal").modal('show')
        })
    });


    $("#mainForm").submit(function(e) {
        e.preventDefault();
        var requestBody = new FormData(this);

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            processData: false,
            contentType: false,
            data: requestBody,
            success: function(data){
                $("#formModal").modal('hide')
                $("#successModal").modal('show');
                $table.ajax.reload();
            },
            error: function(){
                $("#formModal").modal('hide')
                $("#failedModal").modal('show');
            }
        })

    })

    $("#deleteForm").submit(function(e){
        e.preventDefault();
        var requestBody = new FormData(this);
        var This = this

        $("#confirmModal").on('hide.bs.modal', function(){
            if(isActionConfirmed){
                $.ajax({
                    method: This.method,
                    url: This.action,
                    processData: false,
                    contentType: false,
                    data: requestBody,
                    success: function(data){
                        $("#formModal").modal('hide')
                        $("#failedModal").modal('hide');
                        $("#successModal").modal('show');
                        $table.ajax.reload();
                    },
                    error: function(){
                        $("#formModal").modal('hide')
                        $("#successModal").modal('hide');
                        $("#failedModal").modal('show');
                    }
                })
            }
        })

        $("#confirmModal").modal('show')
    })

    $(".confirm-button").click(function (e) {
        if(typeof $(this).attr('data-confirm') == 'string'){
            isActionConfirmed = $(this).attr('data-confirm') == "true"
        } else if(typeof $(this).attr('data-confirm') == 'boolean'){
            isActionConfirmed = $(this).attr('data-confirm')
        }
    })

    $("#formModal").on('hidden.bs.modal', function(){
        $("#reset").click();
        $("#flush").click();
        $("#idInventaris").val('new')
        $("#submit").html("Tambah")
        $("#reset").removeClass("d-none")
        $("#delete").addClass("d-none")
        $table.rows('.selected').deselect()
    })

</script>
@endsection