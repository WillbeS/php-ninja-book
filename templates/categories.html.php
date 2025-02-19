<h2>Categories</h2>

<a href="<?= '/' . $baseUrl . 'category/edit'; ?>">Add a new category</a>

<?php foreach ($categories as $category): ?>
<blockquote>
  <p>
  <?=htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8')?>

  <a href="<?= '/' . $baseUrl . 'category/edit/' . $category->id ?>">Edit</a>

  <form action="<?= '/' . $baseUrl . 'category/delete' ?>" method="post">
    <input type="hidden" name="id" value="<?=$category->id?>">
    <input type="submit" value="Delete">
  </form>
  </p>
</blockquote>

<?php endforeach; ?>