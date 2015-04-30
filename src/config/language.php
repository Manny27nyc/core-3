<?php return [

    'default' => [

        'interpreter' => 'Laravel',

        'translator' => 'Symfony'

    ],


    'translator' => [

        'Symfony' => [
            'catalogs' => [
                'A user was found to match all plain text credentials however hashed credential [password] did not match.' => 'Nama pengguna atau katalaluan salah.',
                'User [%email%] has been suspended.' => 'Pengguna sistem dengan login [%email%] telah di gantung',
                'The password attribute is required.' => 'Sila masukkan katalaluan',
                'The [email] attribute is required.' => 'Sila masukkan email',
                'A user could not be found with a login value of [%email%].' => 'Pengguna dengan login [%email%] tiada dalam sistem'
            ]
        ]

    ]

];