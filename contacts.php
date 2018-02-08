<?php

class contacts
{
    const DEVELOPER_KEY = '';
    const CLIENT_KEY = '';
    const CLIENT_SECRET = '';
    const REDIRCT_URL = 'http://localhost:8080/';

    /**
     * @param object $service
     * @return string $html
     */
    public function displayContacts($service)
    {
        $html = "<strong>People</strong>:<br /><br />";

        // https://developers.google.com/people/api/rest/v1/people.connections/list
        $results = $service->people_connections->listPeopleConnections(
            'people/me',
            [
                'sortOrder' => 'FIRST_NAME_ASCENDING',
                'pageSize' => 1000,
                'personFields' => 'names,emailAddresses,phoneNumbers,birthdays',
            ]
        );

        if (count($results->getConnections()) == 0) {
            $html .= "No connections found.\n";
        } else {
            $contacts = $this->fetchContacts($results);
            $html .= $contacts['html'] . '<br />Total: ' . $contacts['count'];
        }

        return $html;
    }

    /**
     * @param $results
     * @return array
     */
    private function fetchContacts($results)
    {
        $count = 0;
        $html = '';

        foreach ($results->getConnections() as $person) {
            if (count($person->getNames()) == 0) {
                $html .= "No names found for this connection <br />";
            } else {
                $count = $count + 1;
                $names = $person->getNames();
                $phoneNumbers = $person->getPhoneNumbers();
                $name = $names[0];

                $html .= $name->getDisplayName() . ' ' . $phoneNumbers[0]['value'] . '<br />';
            }
        }
        return ['count' => $count, 'html' => $html];
    }
}
