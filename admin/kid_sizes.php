<div class="col3">
<label class="ansog_select_label">SKJORTE STR.</label>
<div class="custom-select">
<!-- shirt size / skjorte fra-->
<select class="shirt_size_from">
<?php
echo "<option value=''> - </option>";
for ($i=86; $i < 171; $i = $i+6) {
$selected = '';
if($i == $value['shirt_size_from']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."cm</option>";
}
?>
</select>
</div>
</div>

<div class="col3">
<label class="ansog_select_label">__</label>
<div class="custom-select">
<!-- shirt size / skskjorteorte til-->
<select class="shirt_size_to">
<?php
echo "<option value=''> - </option>";
for ($i=86; $i < 171; $i = $i+6) { 
$selected = '';
if($i == $value['shirt_size_to']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."cm</option>";
}
?>
</select>
</div>
</div>

<div class="col3">
<label class="ansog_select_label">bukser str.</label>
<div class="custom-select">
<!-- pant size / bukser fra -->
<select class="pants_size_from">
<?php
echo "<option value=''> - </option>";
for ($i=86; $i < 171; $i = $i+6) {
$selected = '';
if($i == $value['pants_size_from']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."cm</option>";
}
?>
</select>
</div>
</div>

<div class="col3">
<label class="ansog_select_label">__</label>
<div class="custom-select">
<!-- pant size / bukser til-->
<select class="pants_size_to">
<?php
echo "<option value=''> - </option>";
for ($i=86; $i < 171; $i = $i+6) { 
$selected = '';
if($i == $value['pants_size_to']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."cm</option>";
}
?>
</select>
</div>
</div>

<div class="col3">
<label class="ansog_select_label">sko str.</label>
<div class="custom-select">
<!-- shoe size / sko fra-->
<select class="shoe_size_from">
<?php
echo "<option value=''> - </option>";
for ($i=17; $i < 43; $i++) { 
$selected = '';
if($i == $value['shoe_size_from']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."''</option>";
}
?>
</select>
</div>
</div>

<div class="col3">
<label class="ansog_select_label">__</label>
<div class="custom-select">
<!-- shoe size / sko til-->
<select class="shoe_size_to">
<?php
echo "<option value=''> - </option>";
for ($i=17; $i < 43; $i++) { 
$selected = '';
if($i == $value['shoe_size_to']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."''</option>";
}
?>
</select>
</div>
</div>
<?php /* ?>
<div class="col3">
<label class="ansog_select_label">borne str.</label>
<div class="custom-select">
<!-- child size / borne til -->
<select class="children_sizes">
<?php
echo "<option value=''> - </option>";
for ($i=1; $i < 4; $i++) { 
$selected = '';
if($i == $value['children_sizes']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."</option>";
}
?>
</select>
</div>
</div>
<?php */ ?>
