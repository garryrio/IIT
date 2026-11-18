<?php

// Pesan validasi Bahasa Indonesia (aturan yang dipakai aplikasi + aturan umum).
return [
    'accepted' => ':Attribute harus disetujui.',
    'after_or_equal' => ':Attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha_dash' => ':Attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'array' => ':Attribute harus berupa daftar.',
    'before_or_equal' => ':Attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':Attribute harus bernilai antara :min dan :max.',
        'string' => ':Attribute harus terdiri dari :min sampai :max karakter.',
        'file' => ':Attribute harus berukuran antara :min sampai :max kilobita.',
        'array' => ':Attribute harus memiliki :min sampai :max item.',
    ],
    'boolean' => ':Attribute harus bernilai ya atau tidak.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'date_format' => ':Attribute tidak sesuai format :format.',
    'dimensions' => 'Dimensi gambar :attribute terlalu besar.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'file' => ':Attribute harus berupa file.',
    'gt' => [
        'numeric' => ':Attribute harus lebih besar dari :value.',
        'string' => ':Attribute harus lebih dari :value karakter.',
    ],
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':Attribute tidak boleh lebih dari :max.',
        'string' => ':Attribute tidak boleh lebih dari :max karakter.',
        'file' => ':Attribute tidak boleh lebih dari :max kilobita.',
        'array' => ':Attribute tidak boleh memiliki lebih dari :max item.',
    ],
    'mimes' => ':Attribute harus berupa file bertipe: :values.',
    'mimetypes' => ':Attribute harus berupa file bertipe: :values.',
    'min' => [
        'numeric' => ':Attribute minimal bernilai :min.',
        'string' => ':Attribute minimal berisi :min karakter.',
        'file' => ':Attribute minimal berukuran :min kilobita.',
        'array' => ':Attribute minimal memiliki :min item.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'required_with' => ':Attribute wajib diisi bila :values diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',
    'uploaded' => ':Attribute gagal diunggah.',
    'url' => 'Format :attribute harus berupa URL yang valid (diawali https://).',

    'custom' => [],

    'attributes' => [
        'email' => 'email',
        'password' => 'kata sandi',
        'q' => 'kata kunci',
        'dari' => 'tanggal mulai',
        'sampai' => 'tanggal akhir',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
    ],
];
