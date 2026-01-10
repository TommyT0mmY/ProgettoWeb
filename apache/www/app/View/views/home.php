<?php 
/** @var \Unibostu\Dto\PostDto[] $posts */

$this->extend('main-layout', ['title' => 'Unibostu - Homepage'
]); 
?>

<div class="post_container">
<?php foreach ($posts ?? [] as $post): ?>
    <?= $this->component('post', ['post' => $post]) ?>
<?php endforeach; ?>
</div>
