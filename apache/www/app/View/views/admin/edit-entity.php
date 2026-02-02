<?php 
/**
 * Generic view for editing/adding admin entities (faculties, courses, tags, categories)
 * 
 * @var \Unibostu\Core\RenderingEngine $this
 * @var string $mode - 'edit' or 'add'
 * @var string $entityType - 'faculty', 'course', 'tag', 'category'
 * @var string $formTitle - Title to display in the legend
 * @var string $formId - ID for the form element
 * @var string $submitEndpoint - API endpoint for form submission
 * @var string $backUrl - URL to navigate back
 * @var array $fields - Array of field definitions
 *   Each field: [
 *     'name' => 'fieldname',
 *     'label' => 'Field Label',
 *     'value' => 'field value',
 *     'type' => 'text|hidden',
 *     'required' => true|false,
 *     'readonly' => true|false (automatically set to true if field name contains 'id')
 *   ]
 * @var string $adminId
 */

$this->extend('admin-layout', [
    'title' => $formTitle . ' - Unibostu',
    'additionalHeadCode' => [
        '<script type="module" src="/js/admin/edit-entity.js"></script>',
        '<link rel="stylesheet" href="/css/fsform.css" />',
    ],
]);
?>
<form class="fullscreen-form" id="<?= h($formId); ?>" method="post" novalidate 
      data-endpoint="<?= h($submitEndpoint); ?>" 
      data-mode="<?= h($mode); ?>"
      data-entity-type="<?= h($entityType); ?>">
    <fieldset>
        <legend><?= h($formTitle); ?></legend>
        <output class="form-error-message" role="alert"></output>
        
        <?php foreach ($fields as $field): ?>
            <?php 
            $fieldName = $field['name'];
            $fieldLabel = $field['label'];
            $fieldValue = $field['value'] ?? '';
            $fieldType = $field['type'] ?? 'text';
            $isRequired = $field['required'] ?? false;
            // Auto-detect readonly for ID fields
            $isReadonly = $field['readonly'] ?? (stripos($fieldName, 'id') !== false && $mode === 'edit');
            ?>
            
            <?php if ($fieldType === 'hidden'): ?>
                <input type="hidden" name="<?= h($fieldName); ?>" value="<?= h($fieldValue); ?>" />
            <?php else: ?>
                <div class="field-holder">
                    <input 
                        type="<?= h($fieldType); ?>" 
                        name="<?= h($fieldName); ?>" 
                        id="<?= h($fieldName); ?>" 
                        value="<?= h($fieldValue); ?>" 
                        <?= $isRequired ? 'required' : ''; ?>
                        <?= $isReadonly ? 'disabled' : ''; ?>
                        <?= $isRequired && !$isReadonly ? 'aria-describedby="' . h($fieldName) . '-error"' : ''; ?>
                    />
                    <label for="<?= h($fieldName); ?>"><?= h($fieldLabel); ?></label>
                    <?php if ($isRequired && !$isReadonly): ?>
                        <output class="field-error-message" id="<?= h($fieldName); ?>-error" for="<?= h($fieldName); ?>"></output>
                    <?php endif; ?>
                </div>
                
                <!-- Add hidden field for readonly inputs so data is submitted -->
                <?php if ($isReadonly): ?>
                    <input type="hidden" name="<?= h($fieldName); ?>" value="<?= h($fieldValue); ?>" />
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <output class="form-status-message" role="status"></output>
        
        <div class="controls-container">            
            <button type="button" onclick="window.location.href='<?= h($backUrl); ?>'">Back</button>
            <button type="submit" id="submit-btn">
                <?= $mode === 'add' ? 'Create' : 'Save Changes'; ?>
            </button>
        </div>
        
    </fieldset>
</form>
