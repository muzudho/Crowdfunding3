<?php
foreach ($this['options'] as $key => $checkbox){
    if(empty($checkbox->category)){
        if($key != 0)
        {
            // 最初の要素以外は、＜ul＞タグを一旦閉じます。
            echo '</ul>';
        }

        // 大見出し
        echo '<p>' . $checkbox->label . '</p>';
        // 開始ulタグ
        echo '<ul';
        if(PC_VIEW){
            echo ' class="heightLineParent"';
        }
        echo '>';

    } else {
        // 小見出し
        echo '<li>' . $checkbox->getInnerHTML() . '</li>';
    }
}

// 終了ulタグ
echo '</ul>';
