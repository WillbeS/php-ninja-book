
<div id="jokes_page">

  <ul class="categories">
    <?php foreach ($categories as $category): ?>
      <li><a href="<?= '/' . $baseUrl . 'joke/list/' . $category->id ?>"><?=$category->name?></a></li>
    <?php endforeach; ?>
  </ul>

  <p><?=$totalJokes?> jokes have been submitted to the Internet Joke Database.</p>

  <?php foreach ($jokes as $joke): ?>
    <div class="jokes-list">
      <blockquote>
        <p>
          
        <?php
        $markdown = new \Ninja\Markdown($joke->joketext);
        echo $markdown->toHtml();
        ?>

          (by <a href="mailto:<?php echo htmlspecialchars($joke->getAuthor()->email, ENT_QUOTES, 'UTF-8'); ?>">
          <?php echo htmlspecialchars($joke->getAuthor()->name, ENT_QUOTES, 'UTF-8'); ?></a> on 
          <?php
          $date = new DateTime($joke->jokedate);
          echo $date->format('jS F Y'); 
          ?>)
        </p>
      </blockquote>

      <?php if ($user): ?>

        <?php if (empty($joke) || $user->id == $joke->authorid || $user->hasPermission(\Ijdb\Entity\Author::EDIT_JOKE)): ?>

          <a href="<?= '/' . $baseUrl . 'joke/edit/' . $joke->id ?>">Edit</a>
        <?php endif; ?>

        <?php if ($user->id == $joke->authorid || $user->hasPermission(\Ijdb\Entity\Author::DELETE_JOKE)): ?>
          <form action="<?= '/' . $baseUrl . 'joke/delete' ?>" method="post">
              <input type="hidden" name="id" value="<?=$joke->id?>">
              <input type="submit" value="Delete">
          </form>
        <?php endif; ?>
        
      <?php endif; ?>

    </div>
  <?php endforeach; ?>

  Select page:

  <?php
  // Calculate the number of pages
  $numPages = ceil($totalJokes/2);
  $category = $categoryId ?: "all";

  // Display a link for each page
  for ($i = 1; $i <= $numPages; $i++): 
    if ($i == $currentPage):?>
      <a class="currentpage" href="/<?=$baseUrl?>joke/list/<?=$category?>/<?=$i?>"><?=$i?></a>
    <?php else: ?>
      <a href="/<?=$baseUrl?>joke/list/<?=$category?>/<?=$i?>"><?=$i?></a>
    <?php endif; ?>
  <?php endfor; ?>

</div>