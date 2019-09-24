<?php

use common\helpers\Html;
use common\enums\StatusEnum;

?>

<div class="form-group">
    <?= Html::label(t($row['title']), $row['name'], ['class' => 'control-label demo']); ?>
    <?php if ($row['is_hide_remark'] != StatusEnum::ENABLED) { ?>
        (<?= t($row['remark']) ?>)
    <?php } ?>
    <?= Html::textarea('config[' . $row['name'] . ']', $row['value']['data'] ?? $row['default_value'],
        ['class' => 'form-control']); ?>
</div>