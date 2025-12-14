<?php include 'header.php'; ?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header d-flex justify-content-between align-items-center">
                    <h4>Daily Production</h4>
                    <button class="btn btn-primary btn-sm" id="addDailyBtn"><i class="fa fa-plus"></i> Add
                        Production</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="m-0">Daily Records</h5>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table id="daily-table" class="table table-bordered table-striped" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th width="120">Date</th>
                                        <th>Material Supplied Value</th>
                                        <th>Value as per Sales Price</th>
                                        <th width="100" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Daily Production Modal -->
<div class="modal fade" id="dailyModal" tabindex="-1" role="dialog" aria-labelledby="dailyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="dailyForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="dailyModalLabel">Add Daily Production</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editDateFlag" value="0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-products" role="tab">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-materials" role="tab">Materials</a>
                        </li>
                    </ul>
                    <div class="tab-content pt-3">
                        <div class="tab-pane active" id="tab-products" role="tabpanel">
                            <div class="form-row mb-3">
                                <div class="col-md-4">
                                    <label for="prodDate">Date</label>
                                    <input type="date" class="form-control" id="prodDate" name="prod_date" required>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="productsTable">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th width="140">Sales Price</th>
                                            <th width="160">No of Production</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productRows">
                                        <tr>
                                            <td colspan="3" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" id="nextToMaterials">Next</button>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-materials" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="materialsTable">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th width="120">Measurement</th>
                                            <th width="140">Current Price</th>
                                            <th width="160">System Calculation</th>
                                            <th width="160">Actual Material Used</th>
                                        </tr>
                                    </thead>
                                    <tbody id="materialRows">
                                        <tr>
                                            <td colspan="5" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary processing">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
let dailyTable = null;
let refData = null;
let refPromise = null;
let allocMap = {};

function fetchReference() {
    if (refPromise) return refPromise;
    refPromise = $.getJSON('ajax/daily_production_reference.php', function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load reference data.', 'error');
            refPromise = null;
            return;
        }
        refData = response.data;
        allocMap = {};
        (refData.allocations || []).forEach(function(a) {
            if (!allocMap[a.product_id]) allocMap[a.product_id] = [];
            allocMap[a.product_id].push(a);
        });
    }).fail(function() {
        refPromise = null;
        Swal.fire('Error', 'Failed to load reference data.', 'error');
    });
    return refPromise;
}

function renderProducts(existingProducts = {}, fallbackProducts = []) {
    const tbody = $('#productRows');
    tbody.empty();
    if (!refData || !refData.products || refData.products.length === 0) {
        tbody.append('<tr><td colspan="3" class="text-center">No products found.</td></tr>');
        return;
    }
    // merge existing + fallback quantities for quick lookup
    const qtyMap = {};
    Object.keys(existingProducts).forEach(function(k) {
        qtyMap[k] = existingProducts[k];
    });
    (fallbackProducts || []).forEach(function(fp) {
        if (fp && fp.product_id !== undefined && qtyMap[fp.product_id] === undefined) {
            qtyMap[fp.product_id] = fp.quantity;
        }
    });
    const rendered = {};
    refData.products.forEach(function(p) {
        const qty = qtyMap[p.p_id] !== undefined ? qtyMap[p.p_id] : '';
        rendered[p.p_id] = true;
        tbody.append(`
            <tr class="prod-row" data-product="${p.p_id}" data-batch="${p.batch_quantity}" data-price="${p.sales_price}">
                <td>${p.product_name}</td>
                <td class="text-center">${parseFloat(p.sales_price).toFixed(2)}</td>
                <td><input type="number" class="form-control form-control-sm prod-qty" min="0" step="1" value="${qty}"></td>
            </tr>
        `);
    });
    // add fallback products not present in refData (e.g., archived)
    fallbackProducts.forEach(function(fp) {
        if (rendered[fp.product_id]) return;
        const qty = qtyMap[fp.product_id] !== undefined ? qtyMap[fp.product_id] : fp.quantity;
        const price = fp.sales_price || 0;
        const batch = fp.batch_quantity || 0;
        tbody.append(`
            <tr class="prod-row" data-product="${fp.product_id}" data-batch="${batch}" data-price="${price}">
                <td>${fp.product_id}</td>
                <td class="text-center">${parseFloat(price).toFixed(2)}</td>
                <td><input type="number" class="form-control form-control-sm prod-qty" min="0" step="1" value="${qty}"></td>
            </tr>
        `);
    });
}

function renderMaterials(existingMaterials = {}, fallbackMaterials = []) {
    const tbody = $('#materialRows');
    tbody.empty();
    if (!refData || !refData.materials || refData.materials.length === 0) {
        tbody.append('<tr><td colspan="5" class="text-center">No materials found.</td></tr>');
        return;
    }
    const qtyMap = {};
    Object.keys(existingMaterials).forEach(function(k) {
        qtyMap[k] = existingMaterials[k];
    });
    (fallbackMaterials || []).forEach(function(fm) {
        if (fm && fm.material_id !== undefined && qtyMap[fm.material_id] === undefined) {
            qtyMap[fm.material_id] = fm.quantity_used;
        }
    });
    const rendered = {};
    refData.materials.forEach(function(m) {
        const actual = qtyMap[m.id] !== undefined ? qtyMap[m.id] : '';
        rendered[m.id] = true;
        tbody.append(`
            <tr class="mat-row" data-material="${m.id}" data-price="${m.current_price}">
                <td>${m.material_name}</td>
                <td class="text-center">${m.mesurement}</td>
                <td class="text-center">${parseFloat(m.current_price).toFixed(2)}</td>
                <td class="text-center sys-calc" data-value="0">0.00</td>
                <td><input type="number" class="form-control form-control-sm mat-actual" min="0" step="0.01" value="${actual}"></td>
            </tr>
        `);
    });
    // add fallback materials not present in refData (e.g., archived)
    fallbackMaterials.forEach(function(fm) {
        if (rendered[fm.material_id]) return;
        const actual = existingMaterials[fm.material_id] !== undefined ? existingMaterials[fm.material_id] : fm
            .quantity_used;
        const price = fm.material_price || 0;
        tbody.append(`
            <tr class="mat-row" data-material="${fm.material_id}" data-price="${price}">
                <td>${fm.material_id}</td>
                <td class="text-center">-</td>
                <td class="text-center">${parseFloat(price).toFixed(2)}</td>
                <td class="text-center sys-calc" data-value="0">0.00</td>
                <td><input type="number" class="form-control form-control-sm mat-actual" min="0" step="0.01" value="${actual}"></td>
            </tr>
        `);
    });
}

function recalcMaterials() {
    const totals = {};
    $('.prod-row').each(function() {
        const pid = $(this).data('product');
        const batch = parseFloat($(this).data('batch')) || 0;
        const qty = parseFloat($(this).find('.prod-qty').val()) || 0;
        if (qty > 0 && batch > 0 && allocMap[pid]) {
            allocMap[pid].forEach(function(a) {
                const perItem = parseFloat(a.unit) / batch;
                const total = qty * perItem;
                totals[a.material_id] = (totals[a.material_id] || 0) + total;
            });
        }
    });

    $('.mat-row').each(function() {
        const mid = $(this).data('material');
        const calc = totals[mid] || 0;
        $(this).find('.sys-calc').text(calc.toFixed(2)).attr('data-value', calc.toFixed(2));
    });
}

function loadSummary() {
    $.getJSON('ajax/daily_production_summary.php', function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load daily records.', 'error');
            return;
        }
        const tbody = $('#daily-table tbody');
        tbody.empty();
        response.data.forEach(function(row) {
            tbody.append(`
                <tr>
                    <td>${row.date}</td>
                    <td>${parseFloat(row.material_value).toFixed(2)}</td>
                    <td>${parseFloat(row.sales_value).toFixed(2)}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary edit-daily" data-date="${row.date}"><i class="fa fa-edit"></i></button>
                    </td>
                </tr>
            `);
        });
        if (dailyTable) {
            dailyTable.destroy();
        }
        dailyTable = $('#daily-table').DataTable({
            order: [
                [0, 'desc']
            ],
            pageLength: 100
        });
    }).fail(function() {
        Swal.fire('Error', 'Failed to load daily records.', 'error');
    });
}

function applyExistingToRows(productList = [], materialList = []) {
    (productList || []).forEach(function(p) {
        if (!p || p.product_id === undefined) return;
        const pid = String(p.product_id);
        const row = $('.prod-row').filter(function() { return String($(this).data('product')) === pid; });
        if (row.length) {
            row.find('.prod-qty').val(p.quantity);
            if (p.batch_quantity !== undefined) {
                row.attr('data-batch', p.batch_quantity);
            }
            if (p.sales_price !== undefined) {
                row.attr('data-price', p.sales_price);
                row.find('td:nth-child(2)').text(parseFloat(p.sales_price).toFixed(2));
            }
        }
    });

    (materialList || []).forEach(function(m) {
        if (!m || m.material_id === undefined) return;
        const mid = String(m.material_id);
        const row = $('.mat-row').filter(function() { return String($(this).data('material')) === mid; });
        if (row.length) {
            row.find('.mat-actual').val(m.quantity_used);
            if (m.material_price !== undefined) {
                row.attr('data-price', m.material_price);
                row.find('td:nth-child(3)').text(parseFloat(m.material_price).toFixed(2));
            }
        }
    });
}

function openModal(dateValue = '', existingProducts = {}, existingMaterials = {}, isEdit = false, fallbackProducts = [],
    fallbackMaterials = []) {
    const today = new Date().toISOString().slice(0, 10);
    $('#prodDate').prop('readonly', isEdit);
    $('#editDateFlag').val(isEdit ? 1 : 0);
    $('#dailyModalLabel').text(isEdit ? 'Edit Daily Production' : 'Add Daily Production');
    $('#prodDate').attr('min', today);
    $('#prodDate').val(dateValue || today);

    renderProducts(existingProducts, fallbackProducts);
    renderMaterials(existingMaterials, fallbackMaterials);
    // ensure values are injected even if items are archived or missing from refData
    applyExistingToRows(fallbackProducts, fallbackMaterials);
    // direct map-based fill (covers currently listed refData rows) with string keys
    $('.prod-row').each(function() {
        const pid = String($(this).data('product'));
        if (existingProducts[pid] !== undefined) {
            $(this).find('.prod-qty').val(existingProducts[pid]);
        }
    });
    $('.mat-row').each(function() {
        const mid = String($(this).data('material'));
        if (existingMaterials[mid] !== undefined) {
            $(this).find('.mat-actual').val(existingMaterials[mid]);
        }
    });
    recalcMaterials();

    $('#dailyModal').modal('show');
}

$(document).ready(function() {
    fetchReference().then(function() {
        loadSummary();
    });

    $('#addDailyBtn').on('click', function() {
        fetchReference().then(function() {
            openModal();
        });
    });

    $('#productRows').on('input', '.prod-qty', function() {
        recalcMaterials();
    });

    $('#nextToMaterials').on('click', function() {
        $('a[href="#tab-materials"]').tab('show');
    });

    $(document).on('click', '.edit-daily', function() {
        const date = $(this).data('date');
        fetchReference().then(function() {
            $.getJSON('ajax/daily_production_detail.php', {
                date: date
            }, function(response) {
                if (!response.success) {
                    Swal.fire('Error', response.message || 'Unable to load record.',
                        'error');
                    return;
                }
                const detailProducts = (response.data && response.data.products) ? response.data.products : (response.products || []);
                const detailMaterials = (response.data && response.data.materials) ? response.data.materials : (response.materials || []);

                const prodMap = {};
                (detailProducts || []).forEach(function(p) {
                    prodMap[p.product_id] = p.quantity;
                });
                const matMap = {};
                (detailMaterials || []).forEach(function(m) {
                    matMap[m.material_id] = m.quantity_used;
                });
                console.log('Daily detail payload', {
                    date,
                    products: detailProducts,
                    materials: detailMaterials,
                    prodMap,
                    matMap
                });
                openModal(date, prodMap, matMap, true, detailProducts || [], detailMaterials || []);
                setTimeout(function() {
                    console.log('After openModal fill', {
                        prodRows: $('.prod-row').map(function() {
                            return {
                                id: $(this).data('product'),
                                qty: $(this).find('.prod-qty').val()
                            };
                        }).get(),
                        matRows: $('.mat-row').map(function() {
                            return {
                                id: $(this).data('material'),
                                qty: $(this).find('.mat-actual').val()
                            };
                        }).get()
                    });
                }, 300);
                $('a[href="#tab-products"]').tab('show');
            }).fail(function() {
                Swal.fire('Error', 'Failed to load record.', 'error');
            });
        });
    });

    $('#dailyForm').on('submit', function(e) {
        e.preventDefault();
        const dateVal = $('#prodDate').val();
        if (!dateVal) {
            Swal.fire('Error', 'Date is required.', 'error');
            return;
        }
        const today = new Date().toISOString().slice(0, 10);
        if ($('#editDateFlag').val() !== '1' && dateVal < today) {
            Swal.fire('Error', 'Only today or future dates are allowed.', 'error');
            return;
        }

        const products = [];
        $('.prod-row').each(function() {
            const pid = $(this).data('product');
            const price = parseFloat($(this).data('price')) || 0;
            const qty = parseFloat($(this).find('.prod-qty').val()) || 0;
            if (qty > 0) {
                products.push({
                    product_id: pid,
                    sales_price: price,
                    quantity: qty
                });
            }
        });
        if (products.length === 0) {
            Swal.fire('Error', 'Enter at least one product quantity.', 'error');
            return;
        }

        const materials = [];
        $('.mat-row').each(function() {
            const mid = $(this).data('material');
            const price = parseFloat($(this).data('price')) || 0;
            const qty = parseFloat($(this).find('.mat-actual').val()) || 0;
            if (qty > 0) {
                materials.push({
                    material_id: mid,
                    material_price: price,
                    quantity_used: qty
                });
            }
        });

        $.post('ajax/daily_production_save.php', {
            date: dateVal,
            products: JSON.stringify(products),
            materials: JSON.stringify(materials),
            is_edit: $('#editDateFlag').val()
        }, function(response) {
            if (response.success) {
                $('#dailyModal').modal('hide');
                Swal.fire('Success', response.message || 'Saved successfully.', 'success');
                loadSummary();
            } else {
                Swal.fire('Error', response.message || 'Unable to save.', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Request failed. Please try again.', 'error');
        });
    });
});
</script>
