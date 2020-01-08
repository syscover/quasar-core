<?php

return [

    //******************************************************************************************************************
    //***   Set fields to save from EXIT image properties to avoid utf-8 characters.
    //***   That they are includes by software like Photoshop
    //******************************************************************************************************************
    'exif_fields_allowed' => [
        'FileName',
        'FileDateTime',
        'FileSize',
        'FileType',
        'MimeTye',
        'SectionsFound',
        'COMPUTED',
        'ImageWidth',
        'ImageLength',
        'BitsPerSample',
        'PhotometricInterpretation',
        'ImageDescription',
        'Orientation',
        'SamplesPerPixel',
        'XResolution',
        'YResolution',
        'ResolutionUnit',
        'Software',
        'DateTime',
        'Title',
        'Comments',
        'Keywords'
    ]
];