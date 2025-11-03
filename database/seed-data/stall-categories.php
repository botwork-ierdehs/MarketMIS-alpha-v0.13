<?php

return [
    [
        'name' => 'Table Rental at Fish Section ',
        'description' => 'For vendors selling fish and other seafood',
        'is_transient' => false,
        'is_table_rental' => true,
        'fee_masterlist_id' => 4, // Fee per table
    ],
    [
        'name' => 'Table Rental at Poultry Section ',
        'description' => 'For chicken and other poultry product vendors',
        'is_transient' => false,
        'is_table_rental' => true,
        'fee_masterlist_id' => 4, // Fee per table
    ],
    [
        'name' => 'Table Rental at Meat Section',
        'description' => 'For vendors selling pork and other meat products',
        'is_transient' => false,
        'is_table_rental' => true,
        'fee_masterlist_id' => 4, // Fee per table
    ],
    [
        'name' => 'Volante With Structure Constructed by the LGU (Razed by Fire) ',
        'description' => 'For transient vendors',
        'is_transient' => true,
        'is_table_rental' => false,
        'fee_masterlist_id' => null,
    ],
    [
        'name' => 'Volante With Structure Constructed by Store Owner ',
        'description' => 'For transient vendors',
        'is_transient' => true,
        'is_table_rental' => false,
        'fee_masterlist_id' => null,
    ],
    [
        'name' => 'Volante No Structure',
        'description' => 'For transient vendors',
        'is_transient' => true,
        'is_table_rental' => false,
        'fee_masterlist_id' => null,
    ],
    [
        'name' => 'Market Stalls',
        'description' => 'For permanent vendors',
        'is_transient' => false,
        'is_table_rental' => false,
        'fee_masterlist_id' => 5, // Fee per area of sqr meter
    ],    
];
