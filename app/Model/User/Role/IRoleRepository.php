<?php
namespace App\Model\User\Role;
//TODO - dodelat, nahradit vsude volani repository injekci interfacu
interface IRoleRepository
{

    public function getTableName() :string;

    /**
     * Vrati data pro select box ve tvaru
     * @return array [id => typ_role]
     */
    public function fetchDataForSelect() :array;

}
