<?php
/**
 * @var \Unibostu\Core\RenderingEngine $this
 * @var string $action
 * @var string $selectedSortOrder
 * @var array<Unibostu\Model\DTO\CategoryDTO> $categories
 * @var int|null $selectedCategoryId
 * @var ?array<Unibostu\Model\DTO\TagDTO> $tags Set this to null to hide the tags filter
 * @var ?array<int> $selectedTags Does nothing if $tags is null
 */
?>
<section class="post-filters">
    <form action="<?=$action?>" method="get" id="filter-form">
        <fieldset>
            <legend>Filter posts</legend>
            <div class="field-holder">
                <select id="filter-type" name="categoryId">
                    <option value="">All categories</option>
                    <?php foreach ($categories ?? [] as $category): ?>
                        <option value="<?= htmlspecialchars($category->categoryId) ?>" <?= $selectedCategoryId == $category->categoryId ? 'selected' : '' ?>><?= htmlspecialchars($category->categoryName) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="filter-type">Category:</label>
            </div>
            <div class="field-holder">
                <select id="ordering" name="sortOrder">
                    <option value="desc" <?= $selectedSortOrder === 'desc' ? 'selected' : '' ?>>Newest post first</option>
                    <option value="asc" <?= $selectedSortOrder === 'asc' ? 'selected' : '' ?>>Oldest post first</option>
                </select>
                <label for="ordering">Order by date:</label>
            </div>
            <?php if (isset($tags) && $tags !== null): ?>
            <fieldset>
                <legend class="sub-legend">Filter by tags</legend>
                <?php foreach ($tags ?? [] as $tag): ?>
                    <p>
                        <input type="checkbox" name="tags[]" id="tag_<?= htmlspecialchars($tag->tagId) ?>" value="<?= htmlspecialchars($tag->tagId) ?>" <?= in_array($tag->tagId, $selectedTags) ? 'checked' : '' ?> />
                        <label for="tag_<?= htmlspecialchars($tag->tagId) ?>"><?= htmlspecialchars($tag->tag_name) ?></label>
                    </p>
                <?php endforeach; ?>
            </fieldset>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Filter</button>
        </fieldset>
    </form>
</section>
