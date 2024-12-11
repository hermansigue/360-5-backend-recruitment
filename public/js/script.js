// Variable 
const baseUrl = 'http://localhost:8000';
const pathApiProducts = '/api/shopify/products';
const pathApiDetailProduct = '/api/shopify/products/:id';

function setTable(tableSetBody, dataSet){
    tableSetBody.empty();
    if(dataSet.length > 0){
        dataSet.forEach((item) => {
            let row;
            if(item.quantity > 3 && item.quantity < 10){
                row = $('<tr class="warning">');  
            } else if(item.quantity <= 3){
                row = $('<tr class="danger">');  
            }else{
                row = $('<tr>');  
            }
            row.append($('<td>').text(item.variantId));
            row.append($('<td>').text(item.title));
            row.append($('<td>').text(item.quantity));
            tableSetBody.append(row);
        });
    } else {
        const row = $('<tr>');
        row.append($('<td colspan=3 style="text-align:center">').text('Data is empty'));
        tableSetBody.append(row);
    }
}

function request(url, method, data) {
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: url,
            method: method,
            data: data,
            dataType: 'json',
            success: function(response) {
                resolve(response);
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
    });
}

// wrapper ajax for async await mechanism
async function fetchData(url, method, body) {
    try {
        const data = await request(url, method, body);
        return data;
    } catch (error) {
        console.error('Error occurred:', error);
        alert(error);
    }
}

$('#variant-select').on('select2:open', function() {
    document.querySelector('.select2-search__field').focus();
});

$(document).ready(async function() {
    // Data for select
    // Because the Shopify API is not capable of searching title with wildcards,
    // currently, it only retrieves 8 product data sets initially.
    const selectList = await fetchData(`${baseUrl}${pathApiProducts}`,'GET');
    const productSelect = $('#variant-select').select2({
        data: selectList.map((product) => ({id: product.id, text: product.title})),
        minimumInputLength: 0,
        allowClear: true,
        placeholder: "Select a Product", 
    });
    // End of data select

    // Data for table
    const tableSetBody = $('#table-set tbody');
    let dataSet = [];
    setTable(tableSetBody, dataSet);
    // End of data table
    
    
    
    productSelect.on('change', async function() {
        var selectedOption = $(this).find('option:selected');
        var value = selectedOption.val();
        const finalPathApi = pathApiDetailProduct.replace(':id', value);
        const productFound = await fetchData(`${baseUrl}${finalPathApi}`,'GET');
        dataSet = [];
        if(productFound){
            dataSet = productFound?.variants?.map((variant) => ({
                variantId: variant?.id,
                title: variant?.title,
                quantity: variant?.inventory_quantity ?? 0,
            }))
        }
        setTable(tableSetBody, dataSet);
    });
});