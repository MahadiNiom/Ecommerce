<form action="/products" method="POST">
    @csrf
    <!-- Form fields will go here -->
    <input type="text" name="name" placeholder="Product Name">
    <button type="submit">Create Product</button>
</form>