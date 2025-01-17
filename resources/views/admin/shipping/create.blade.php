@extends('admin.layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manajemen Pengiriman</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('shipping.create') }}" class="btn btn-primary">Kembali</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <form action="" method="POST" id="shippingForm" name="shippingForm">
                <div class= "card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <select name="country" id="country" class="form-control">
                                        <option value="">Pilih Negara</option>
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                            <option value="semua_negara">Semua Negara</option>
                                        @endif
                                    </select>
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <input type="text" name="amount" id="amount" class="form-control"
                                        placeholder="Ongkos kirim" autofocus>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class= "card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                    @if ($shippingCharges->isNotEmpty())
                                        @foreach ($shippingCharges as $shippingCharge)
                                            <tr>
                                                <td>{{ $shippingCharge->id }}</td>
                                                <td>{{ $shippingCharge->country_id == 'semua_negara' ? 'Semua Negara' : $shippingCharge->name }}
                                                </td>
                                                <td>@rupiah($shippingCharge->amount)</td>
                                                <td>
                                                    <a href="{{ route('shipping.edit', $shippingCharge->id) }}"
                                                        class="btn btn-primary">Edit</a>
                                                    <a href="javascirpt:void(0)"
                                                        onClick="deleteRecord({{ $shippingCharge->id }})"
                                                        class="btn btn-danger">Hapus</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#shippingForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);

            $.ajax({
                url: '{{ route('shipping.store') }}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);

                    if (response["status"] == true) {

                        window.location.href = "{{ route('shipping.create') }}";


                    } else {
                        var errors = response['errors'];
                        if (errors['country']) {
                            $("#country").addClass("is-invalid")
                                .siblings('p').addClass('invalid-feedback')
                                .html(errors['country']);
                        } else {
                            $("#country").removeClass("is-invalid")
                                .siblings('p').removeClass('invalid-feedback')
                                .html("");
                        }
                        if (errors['amount']) {
                            $("#amount").addClass("is-invalid")
                                .siblings('p').addClass('invalid-feedback')
                                .html(errors['amount']);
                        } else {
                            $("#amount").removeClass("is-invalid")
                                .siblings('p').removeClass('invalid-feedback')
                                .html("");
                        }
                    }
                },
                error: function(jqXHR, execption) {
                    console.log("Something went wrong");
                }
            })
        });


        function deleteRecord(id) {
            var url = '{{ route('shipping.delete', 'ID') }}';
            var newUrl = url.replace("ID", id)

            Swal.fire({
                title: 'Apakah kamu ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: newUrl,
                        type: 'delete',
                        data: {},
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response["status"]) {

                                window.location.href = "{{ route('shipping.create') }}";
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
