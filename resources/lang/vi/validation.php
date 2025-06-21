<?php

return [
    'required' => 'Trường :attribute là bắt buộc.',
    'email' => 'Trường :attribute phải là một địa chỉ email hợp lệ.',
    'unique' => 'Trường :attribute đã tồn tại.',
    'max' => [
        'string' => 'Trường :attribute không được vượt quá :max ký tự.',
    ],
    'min' => [
        'string' => 'Trường :attribute phải có ít nhất :min ký tự.',
    ],
    'exists' => 'Giá trị đã chọn cho :attribute không hợp lệ.',
    'string' => 'Trường :attribute phải là một chuỗi ký tự.',
    // Thêm các rule khác nếu cần

    'attributes' => [
        'email' => 'email',
        'full_name' => 'họ tên',
        'password' => 'mật khẩu',
        'phone_number' => 'số điện thoại',
        'role_id' => 'vai trò',
    ],
]; 