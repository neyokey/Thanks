<?php
$this->Csv->addRow($th);
foreach($td as $t) {
	foreach ($t as $val) {
	    $this->Csv->addField($val);
	}
    $this->Csv->endRow();
}
$this->Csv->setFilename($filename);

echo $this->Csv->render(true, 'sjis', 'utf-8');
//echo $this->Csv->render();
