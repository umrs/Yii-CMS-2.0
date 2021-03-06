<?
$labels = array(
    'plus'      => t('поствить минус'),
    'minus'     => t('поставить плюс'),
    'not_guest' => t('ставить оценки могут только зарегистрированные пользователи'),
    'not_owner' => t('нельзя ставить оценки самому себе'),
    'exists'    => t('вы уже поставили оценку')
);

$minus_na  = (int) $value < 0 ? 'glyphicon-na' : '';
$plus_na   = (int) $value > 0 ? 'glyphicon-na' : '';
?>

<div class="rating">
    <?= $rating ?>

    <? if (Yii::app()->user->isGuest): ?>
        <div title="<?= $labels['not_guest'] ?>" class="rating-vote minus-na glyphicon-thumbs-down"></div>
        <div title="<?= $labels['not_guest'] ?>" class="rating-vote plus-na glyphicon-thumbs-up"></div>
    <? else: ?>
        <? if ($user_id != Yii::app()->user->id): ?>
            <div title="<?= $labels['minus'] ?>" class="rating-vote minus glyphicon-thumbs-down <?= $minus_na ?>" value="-1" object_id="<?= $object_id ?>" model_id="<?= $model_id ?>"></div>
            <div title="<?= $labels['plus'] ?>" class="rating-vote plus glyphicon-thumbs-up <?= $plus_na ?>" value="1" object_id="<?= $object_id ?>" model_id="<?= $model_id ?>"></div>
        <? else: ?>
            <div title="<?= $labels['not_owner'] ?>" class="rating-vote minus glyphicon-thumbs-down glyphicon-na"></div>
            <div title="<?= $labels['not_owner'] ?>" class="rating-vote plus glyphicon-thumbs-up glyphicon-na"></div>
        <? endif ?>
    <? endif ?>
</div>

