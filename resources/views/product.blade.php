<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Product CRUD</h2>

    <!-- Form -->
    <form id="productForm">
        <input type="hidden" id="product_id">
        <div class="mb-3">
            <input type="text" id="product_name" class="form-control" placeholder="Product Name" required>
        </div>
        <div class="mb-3">
            <input type="number" id="product_price" class="form-control" placeholder="Product Price" required>
        </div>
        <div class="mb-3">
            <textarea id="product_description" class="form-control" placeholder="Description"></textarea>
        </div>
        <div class="mb-3">
            <input type="file" id="product_images" class="form-control" multiple accept=".png,.jpg,.jpeg,.gif,.svg"g>
        </div>
        <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
        <button type="button" class="btn btn-secondary" id="btnCancel" style="display:none;">Cancel</button>
    </form>

    <hr>

    <!-- Table -->
    <table class="table table-bordered mt-4" id="productsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Load products
    function loadProducts(){
        $.getJSON("/api/products", function(data){
            let tbody = "";
            data.data.forEach(p => {
                let imagesHtml = '';
                if(p.product_images && p.product_images.length > 0){
                    p.product_images.forEach(img => {
                        imagesHtml += `<img src="/storage/${img}" width="50" class="me-1"/>`;
                    });
                }
                tbody += `<tr>
                    <td>${p.id}</td>
                    <td>${p.product_name}</td>
                    <td>${p.product_price}</td>
                    <td>${p.product_description ?? ''}<br>${imagesHtml}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editBtn" data-id="${p.id}">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${p.id}">Delete</button>
                    </td>
                </tr>`;
            });
            $('#productsTable tbody').html(tbody);
        });
    }

    loadProducts();

    // Save / Update
    $('#productForm').submit(function(e){
    e.preventDefault();
    let id = $('#product_id').val();
    let url = id ? `/api/products/${id}` : '/api/products';
    let type = id ? 'POST' : 'POST'; // We'll use POST with _method for PUT

    let formData = new FormData();
    formData.append('product_name', $('#product_name').val());
    formData.append('product_price', $('#product_price').val());
    formData.append('product_description', $('#product_description').val());

    // Append images
    let files = $('#product_images')[0].files;
    for(let i=0; i<files.length; i++){
        formData.append('product_images[]', files[i]);
    }

    // If updating, add _method for PUT
    if(id){
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res){
            alert('Saved successfully!');
            $('#productForm')[0].reset();
            $('#product_id').val('');
            $('#btnCancel').hide();
            loadProducts();
        },
        error: function(err){
            alert('Error: ' + JSON.stringify(err.responseJSON.errors));
        }
    });
});


    // Edit
    $(document).on('click', '.editBtn', function(){
        let id = $(this).data('id');
        $.getJSON(`/api/products/${id}`, function(p){
            $('#product_id').val(p.id);
            $('#product_name').val(p.product_name);
            $('#product_price').val(p.product_price);
            $('#product_description').val(p.product_description);
            $('#btnCancel').show();
        });
    });

    // Cancel edit
    $('#btnCancel').click(function(){
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $(this).hide();
    });

    // Delete
    $(document).on('click', '.deleteBtn', function(){
        if(confirm('Are you sure to delete?')){
            let id = $(this).data('id');
            $.ajax({
                url: `/api/products/${id}`,
                type: 'DELETE',
                success: function(res){
                    alert('Deleted successfully!');
                    loadProducts();
                }
            });
        }
    });

});
</script>
</body>
</html>
