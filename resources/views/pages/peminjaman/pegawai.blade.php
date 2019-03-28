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
 
@section('content')

<div class="modal" id="formModal">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title">Inventaris</h5>
                <button class="close" data-dismiss="modal" aria-label="close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="container-fluid">
                <table class="datatable table table-striped table-hover" id="inventaris-table">
                    <thead class="thead-secondary">
                        <th>ID</th>
                        <th>ID Jenis</th>
                        <th>ID Ruang</th>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kondisi</th>
                        <th>Stok</th>
                        <th>Jenis</th>
                        <th>Ruang</th>
                        <th>Petugas</th>
                        <th>Tanggal Register</th>
                        <th>Keterangan</th>
                    </thead>
                </table>
                <form id="addForm">
                    <div class="row align-items-center">
                        <input type="hidden" form="mainForm" name="id_inventaris" id="id_inventaris">
                        <div class="col-12 col-lg-4 form-group">
                            <label for="nama">Nama Barang</label>
                            <input type="text" name="nama_inventaris" placeholder="Pilih dari tabel" id="nama" class="form-control" readonly="readonly"
                                required>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 form-group mt-3 mt-lg-0">
                            <label for="stock">Stok</label>
                            <div class="input-group">
                                <input type="number" value="0" name="stock" id="stock" class="form-control" readonly required>
                                <div class="input-group-append">
                                    <span class="input-group-text">Unit</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 form-group mt-3 mt-lg-0">
                            <label for="takeaway">Jumlah pinjam</label>
                            <div class="input-group">
                                <input type="number" name="takeaway" id="takeaway" class="form-control" min="1" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">Unit</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-2">
                            <button type="submit" id="addBtn" class="btn btn-secondary btn-block mt-3">Tambah</button>
                        </div>
                        {{--
                        <div class="col-12">
                            <button type="button" id="addBtn" class="btn btn-lg btn-success btn-block">Tambah</button>
                        </div> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container py-4">
    {{--
    <h1>Peminjaman</h1> --}}

    <div class="row">
        <div class="col-12">
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible show" role="alert">
                <h4>Error!</h4>
                @foreach ($errors->all() as $error)
                <p>{{$error}}</p>
                @endforeach
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif @if (isset($submitError))
            <div class="alert alert-warning alert-dismissible show" role="alert">
                <h4>Error!</h4>
                @foreach ($submitError as $error)
                <p>{{$error}}</p>
                @endforeach
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            @endif
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h4>Form Peminjaman</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 form-group">
                            <label for="takeaway_date">Tanggal Pinjam</label>
                            <input form="mainForm" type="date" value="{{date('Y-m-d')}}" min="{{date('Y-m-d')}}" name="takeaway_date" id="takeaway_date"
                                class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label for="return_date">Tangal Kembali</label>
                            <input form="mainForm" type="date" name="return_date" value="{{date('Y-m-d')}}" min="{{date('Y-m-d')}}" id="return_date"
                                class="form-control" required>
                        </div>
                    </div>
                    <form action="{{url('peminjaman/add')}}" method="post" id="mainForm">
                        @csrf
                        <div id="takeaways"></div>
                        <h5>Barang dipinjam</h5>
                        <table class="datatable table table-striped table-hover" id="takeaway-table">
                            <thead class="thead-primary">
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Stok</th>
                                <th>Dipinjam</th>
                                <th></th>
                            </thead>
                        </table>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-secondary btn-block my-4" data-toggle="modal" data-target="#formModal">Tambah Barang</button>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-success btn-block my-4">Pinjam</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
 
@section('more-js-after-body')
<script>
    var $table, $takeawayTable;
    var selectedRow;
    const checkItemURL = "{{url('api/peminjaman/checkItem')}}"
    var itemIDrowIndex = {};
    var itemCount = 0;

    $(document).ready(function(){
        $table = $("#inventaris-table").DataTable({
            ordering: false,
            serverSide: true,
            ajax:'{{url("api/inventaris/get")}}',
            select: 'single',
            dom: 'fSrtip',
            autoWidth: false,
            columnDefs: [
                {
                    targets: [0, 1, 2, 7, 10, 11, 12],
                    visible:false
                }
            ],
            buttons: ['excel', 'pdf', 'print']
        });

        $takeawayTable = $("#takeaway-table").DataTable({
            ordering: false,
            serverSide: false,
            select: false,
            dom: 'tip',
            paging: false,
            autoWidth:false,
            columnDefs: [
                {
                    targets: [0],
                    width: "0px"
                }
            ]
        });

        $table.on('select', function(e, dataTable, type, indexes){
            var selectedItem = $table.rows(indexes).data()[0];
            $("#id_inventaris").val(selectedItem[0])
            $("#nama").val(selectedItem[5])
            $("#stock").val(selectedItem[8]);

            if($("#inv" + selectedItem[0]).html() === undefined){
                $("#takeaway").val(0);
                $("#addBtn").html("Tambah")
            } else {
                $("#takeaway").val($("#inv" + selectedItem[0]).val());
                $("#addBtn").html("Ubah")
            }
            $takeawayTable.rows('.selected').deselect()

            checkAvailability();
            $("#takeaway").focus();
        })

        // $takeawayTable.on('select', function(e, dataTable, type, indexes){
        //     // selectedRow = indexes;
        //     var selectedItem = $takeawayTable.rows(indexes).data()[0];
        //     $("#id_inventaris").val(selectedItem[0]);
        //     $("#nama").val(selectedItem[1]);
        //     $("#stock").val(selectedItem[2]);
        //     $("#takeaway").attr("max", selectedItem[2]);
        //     $("#takeaway").val(selectedItem[3]);
        //     $table.rows('.selected').deselect()
        //     $("#takeaway").focus();
        //     $("#addBtn").html("Ubah")
        // })
    })

    $("#itemCheckBtn").click(checkAvailability)
    $("#takeaway_date").change(checkAvailability)
    $("#return_date").change(checkAvailability)

    function checkAvailability(){
        var formData = new FormData(document.getElementById("mainForm"))

        $.ajax({
            method:'POST',
            url: checkItemURL,
            processData: false,
            contentType: false,
            data: formData,
            success: function(data){
                var {stock} = data;
                $("#stock").val(stock);
                $("#takeaway").attr("max", stock);
            },
            error: function(){
                alert("Failed");
            }
        })
    }

    $("#addForm").submit(function(e){
        e.preventDefault();
        var idInventaris = $("#id_inventaris").val();
        var takeaway = $("#takeaway").val();
        var formTemplate = 
            `<input type="text" name="inventaris_id[]" value="${idInventaris}" class="form-control-plaintext" readonly="readonly">;
             <input type="text" value="${$("#nama").val()}" class="form-control-plaintext" readonly="readonly">;
             <input type="number" value="${$("#stock").val()}" class="form-control-plaintext" readonly="readonly">;
             <input type="number" name="amount[]" id="inv${idInventaris}" value="${takeaway}" max="${$("#stock").val()}" class="form-control">;
             <button type="button" class="btn btn-danger btn-block deleterow" onclick="deleteRow(this)" data-deleterow="${itemCount}">Delete</button>`

        if($("#inv" + idInventaris).html() === undefined){
            $takeawayTable.row.add(formTemplate.split(";")).draw()
            itemIDrowIndex[idInventaris] = itemCount++;
        } else {
            $("#inv" + idInventaris).val(takeaway)
        }

        $("#takeaway").val(0);
        $("#id_inventaris").val("")
        $("#nama").val("")
        $("#stock").val(0);
        $table.rows('.selected').deselect()
        $("#addBtn").html("Tambah")
        $("#formModal").modal('hide')
    })

    // $("#takeaway-table tbody").on('click', 'button.deleterow', function(){
    //     $takeawayTable.row($this.attr("data-deleterow")).remove().draw();
    // })

    function deleteRow(node){
        // document.getElementById().parentNode.parentNode
        var tableRow = node.parentNode.parentNode;
        $takeawayTable.row(tableRow).remove().draw();
    }

</script>
@endsection