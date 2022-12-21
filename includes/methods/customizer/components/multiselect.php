<?php

class Growtype_Search_Multiple_Select extends WP_Customize_Control
{

    /**
     * The type of customize control being rendered.
     */
    public $type = 'multiple-select';

    /**
     * Displays the multiple select on the customize screen.
     */
    public function render_content()
    {

        if (empty($this->choices)) {
            return;
        }
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php if (isset($this->description)) { ?>
                <span class="customize-control-description" style="margin-bottom: 15px;"><?php echo esc_html($this->description); ?></span>
            <?php } ?>
            <select <?php $this->link(); ?> multiple="multiple" style="height: 100%;">
                <?php
                foreach ($this->choices as $value => $label) {
                    $selected = is_array($this->value()) && in_array($value, $this->value()) ? selected(1, 1, false) : '';

                    if (empty($this->value())) {
                        $selected = isset($this->default) && $value === $this->default ? selected(1, 1, false) : '';
                    }

                    echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . $label . '</option>';
                }
                ?>
            </select>
        </label>
    <?php }
}
