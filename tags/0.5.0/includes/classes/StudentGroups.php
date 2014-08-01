<?php
class StudentGroups extends Groups {
	public static $type = 'studentgroup';
	public static $fields = array('studentgroup_id', 'owner_id', 'inst_id', 'name', 'description');
}