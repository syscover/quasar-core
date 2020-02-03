<?php namespace Quasar\Core\Traits;

trait CanManageDataLang
{
    /**
     * Function to add lang record to data_lang column
     *
     * @access	public
     */
    public function addDataLang()
    {
        // create static model instance
        $model = new static;
        $dataLang = [];

        $n = $model::where('common_uuid', $this->commonUuid)->count();

        if ($n === 1)
        {
            $dataLang[] = $this->langUuid;
        }
        else
        {
            // get record with the same common uuid
            $object = $model::where('common_uuid', $this->commonUuid)
                ->where('uuid', '<>', $this->uuid)
                ->first();
            
            // get data_lang from object, check that has array in data_lang column
            $dataLang = is_array($object->dataLang) ? $object->dataLang : [];

            // add new language
            $dataLang[] = $this->langUuid;
        }

        // updates all objects with new language variables
        $model::where('common_uuid', $this->commonUuid)
            ->update([
                'data_lang' => json_encode($dataLang)
            ]);
        
        // set data lang field
        $this->dataLang = $dataLang;
    }

    /**
     * Function to delete lang record from data_lang column
     *
     * @param   string  $langUuid
     * @param   string  $uuid
     */
    public function deleteDataLang()
    {
        // create static model instance
        $model      = new static;
        $dataLang   = $this->dataLang;

        $index = array_search($this->langUuid, $dataLang);
        array_splice($dataLang, $index, 1);
            
        $model::where('common_uuid', $this->commonUuid)
            ->update([
                'data_lang' => json_encode($dataLang)
            ]);
    }
}
