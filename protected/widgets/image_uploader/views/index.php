<div>
    <?php 
    if(!empty($model->$attribute))
        $imgURL = Yii::app()->baseUrl.'/'.$model->$attribute;
    else $imgURL='';
    echo CHtml::image($imgURL, 'Image Preview', array('id' => $previewId, 'width' => $previewWidth, 'height' => $previewHeight)) ?>
    <div>
        <?php
        if (!empty($name)) {
            echo CHtml::fileField($name, $value, $htmlOptions);
        } elseif (!empty($model) && !empty($attribute)) {

            $htmlOptions['onchange']="
                var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#$previewId').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
            var o = input.files[0];
            $('#_file_size').remove();
            var fileinfo = $('<div id=\'_file_size\'></div>');
            fileinfo.text('File Size:'+filesize(o.size));
            $(input).after(fileinfo);
        }";

            echo CHtml::activeFileField($model, $attribute, $htmlOptions);
            echo CHtml::error($model, $attribute);
        }
        ?>        
    </div>
</div>