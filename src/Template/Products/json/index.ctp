<?php
$xml = Json::fromArray(['response' => $recipes]);
echo json_encode($xml->asJson());
?>