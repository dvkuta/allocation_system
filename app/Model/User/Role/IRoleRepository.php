<?php
namespace App\Model\User\Role;
//TODO - dodelat, nahradit vsude volani repository injekci interfacu
interface IRoleRepository
{

    public function getTableName();

    public function fetchDataForSelect() :array;

}
