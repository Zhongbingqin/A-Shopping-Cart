<?php
require_once('ProductList.php');

$a = (isset($_GET['a'])) ? $_GET['a'] : 'home';
require_once('shopping_cart.php');

$cart = new shopping_cart();


if ($a == 'cart') {
    $cartContents = '
	<div class="alert">
		No products in your shopping cart!
	</div>';

    if (isset($_POST['empty'])) {
        $cart->delete();
    }
 
    if (isset($_POST['addProducts'])) {
        foreach ($products as $product) {
            if ($_POST['id'] == $product['name']) {
                break;
            }
        }
        $cart->addProducts($product['name']);
    }

    if (isset($_POST['remove'])) {
        foreach ($products as $product) {
            if ($_POST['id'] == $product['name']) {
                break;
            }
        }
        $cart->remove($product['name']);
    }

    if (!$cart->isEmpty()) {
        $totalCart = 0;
        $allItems = $cart->getProducts();
        $cartContents = '
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="col-md-7">Products</th>
					<th class="col-md-2 text-center">Price</th>
					<th class="col-md-2 text-center">Quantity</th>
					<th class="col-md-2 text-center">Total Price</th>
				</tr>
			</thead>
			<tbody>';
        foreach ($allItems as $id => $items) {
            foreach ($items as $item) {
                foreach ($products as $product) {
                    if ($id == $product['name']) {
                        break;
                    }
                }
                $cartContents .= '
				<tr>
					<td>' . $product['name']. '</td>
					<td class="text-center">$' . number_format($product['price'],2,".",",") . '</td>
					<td class="text-center">' . $item['quantity'] . '</td>
					<td class="text-center">$' . number_format($product['price']* $item['quantity'],2,".",","). '</td>
				    <td <button class="btn btn-warning btn-remove" data-id="' . $id . '" data-color="' . '"><i class="fa fa-trash"></i></button></td>
			    </tr>';
                $totalCart = $totalCart + ($product['price']*$item['quantity']);
            }
        }
        $cartContents .= '
			</tbody>
		</table>
		<div class="text-right">
			<h3>Total: ' . number_format($totalCart, 2, '.', ',') . '</h3>
		</div>
		<p>
			<div class="pull-left">
				<button class="btn btn-danger btn-empty-cart">Empty Cart</button>
			</div>
			<div class="pull-right text-right">
				<a href="?a=home">Continue Shopping</a>
		    </div>
		</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ezyVet</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    
    <style>
        body{margin-top:50px;margin-bottom:200px}
    </style>
</head>

<body>
<div class="navbar navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a href="?a=shop" class="navbar-brand">Products</a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="navbar-collapse collapse" id="navbar-main">
            <ul class="nav navbar-nav">
                <li><a href="?a=cart" id="li-cart">Cart (<?php echo $cart->getAllProducts(); ?>)</a></li>
            </ul>
        </div>
    </div>
</div>

<?php if ($a == 'cart'): ?>
    <div class="container">
        <h1>Cart</h1>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <?php echo $cartContents; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="container">
        <h2>Products</h2>
        <div class="row">
            <?php
            foreach ($products as $product) {
                echo '
					<div class="col-md-6">
						<h4>' . $product['name'] . '</h4>
						<div>
						    <h5>$' . $product['price'] . '</h5>
						         
							<form>
                                <input type="hidden" value="' . $product['name'] . '" class="product-id" />';
                echo '
                                <div class="form-group">
                                <button class="btn btn-success add-to-cart">add to Cart</button>    
                                </div>
                            </form>
                        
							<div class="deletefix"></div>
						</div>
					</div>';
            }
            ?>
        </div>
    </div>
<?php endif; ?>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script>
    $(document).ready(function(){
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }

        $('.add-to-cart').on('click', function(e){
            e.preventDefault();
            const $btn = $(this);
            const id = $btn.parent().parent().find('.product-id').val();
            const qty = $btn.parent().parent().find('.quantity').val();
            const $form = $('<form action="?a=cart" method="post" />').html('<input type="hidden" name="addProducts" value="">' +
                '<input type="hidden" name="id" value="' + id + '">' +
                '<input type="hidden" name="qty" value="' + qty + '">');
            $('body').append($form);
            $form.submit();
            // $form.remove();

        });
        $('.btn-remove').on('click', function(){
            const $btn = $(this);
            const id = $btn.attr('data-id');
            const $form = $('<form action="?a=cart" method="post" />').html('<input type="hidden" name="remove" value="">' +
                '<input type="hidden" name="id" value="'+id+'">');
            $('body').append($form);
            $form.submit();
            
        });
        $('.btn-empty-cart').on('click', function(){
            const $form = $('<form action="?a=cart" method="post" />').html('<input type="hidden" name="empty" value="">');
            $('body').append($form);
            $form.submit();
        });
    });
</script>
</body>
</html>
