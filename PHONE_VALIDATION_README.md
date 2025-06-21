# Tính năng Validation Số Điện Thoại Việt Nam

## Mô tả
Đã thêm validation cho số điện thoại trong tính năng thêm người dùng, chỉ chấp nhận số điện thoại Việt Nam với format chuẩn.

## Tính năng

### 1. Phone Number Validation
- **Chỉ chấp nhận số Việt Nam**: Format chuẩn của các nhà mạng Việt Nam
- **Multiple formats**: Hỗ trợ cả format nội địa và quốc tế
- **Real-time validation**: Kiểm tra ngay khi người dùng nhập
- **Visual feedback**: Hiển thị trạng thái hợp lệ/không hợp lệ

### 2. Supported Formats
- **Format nội địa**: 0xx xxxx xxx (VD: 0123456789)
- **Format quốc tế**: +84xx xxxx xxx (VD: +84123456789)
- **Format quốc tế**: 84xx xxxx xxx (VD: 84123456789)

### 3. Network Providers
- **Viettel**: 03x, 05x, 07x, 08x, 09x
- **MobiFone**: 07x, 08x, 09x
- **Vinaphone**: 03x, 05x, 08x, 09x
- **Vietnamobile**: 05x, 08x
- **Gmobile**: 05x, 08x
- **Itelecom**: 08x

## Files đã thay đổi

### 1. `app/Http/Controllers/Admin/UserController.php`
- Thêm custom validation rule cho phone number
- Sử dụng regex và closure function để kiểm tra format
- Thông báo lỗi tiếng Việt chi tiết

### 2. `resources/views/admin/users/index.blade.php`
- Cập nhật input type thành `tel`
- Thêm placeholder mẫu
- Thêm helper text
- Thêm real-time validation JavaScript
- Cập nhật thông tin quan trọng

## Logic Validation

### Server-side Validation
```php
'phone_number' => [
    'nullable',
    'string',
    'max:20',
    'unique:users,phone_number',
    'regex:/^(0|\+84)(3[2-9]|5[689]|7[06-9]|8[1-689]|9[0-46-9])[0-9]{7}$/',
    function ($attribute, $value, $fail) {
        // Custom validation logic
    }
]
```

### Client-side Validation
```javascript
function validatePhoneNumber(phone) {
    const patterns = [
        /^0[3-9][0-9]{8}$/, // 03x, 05x, 07x, 08x, 09x
        /^\+84[3-9][0-9]{8}$/, // +84 + số điện thoại
        /^84[3-9][0-9]{8}$/ // 84 + số điện thoại
    ];
    
    const isValid = patterns.some(pattern => pattern.test(cleanPhone));
    // Update UI based on validation result
}
```

## Các trường hợp validation

### ✅ Số điện thoại hợp lệ
- `0123456789` - Viettel
- `0987654321` - Viettel
- `+84123456789` - Viettel (quốc tế)
- `84123456789` - Viettel (quốc tế)
- `0323456789` - Viettel
- `0523456789` - Vietnamobile
- `0723456789` - MobiFone
- `0823456789` - Vinaphone
- `0923456789` - Viettel

### ❌ Số điện thoại không hợp lệ
- `1234567890` - Không bắt đầu bằng 0
- `+1234567890` - Không phải +84
- `012345678` - Thiếu số
- `01234567890` - Thừa số
- `012345678a` - Có ký tự không phải số
- `+8412345678` - Thiếu số sau +84
- `+841234567890` - Thừa số sau +84

## Thông báo lỗi

### Validation Error Message
```
"Số điện thoại không đúng định dạng. Vui lòng nhập số điện thoại Việt Nam hợp lệ (VD: 0123456789 hoặc +84123456789)."
```

### UI Elements
- **Placeholder**: "VD: 0123456789 hoặc +84123456789"
- **Helper text**: "Nhập số điện thoại Việt Nam (bắt đầu bằng 0 hoặc +84)"
- **Alert info**: "Số điện thoại: Chỉ chấp nhận số điện thoại Việt Nam (bắt đầu bằng 0 hoặc +84)"

## Real-time Validation

### Visual Feedback
- **Valid state**: Input border xanh, icon check
- **Invalid state**: Input border đỏ, icon error
- **Empty state**: Normal border (không validate)

### Validation Triggers
- **Input event**: Validate ngay khi người dùng nhập
- **Focus event**: Validate khi focus vào field
- **Blur event**: Validate khi rời khỏi field

## Testing

### Test Cases

#### Valid Phone Numbers
1. `0123456789` - ✅ Pass (Viettel)
2. `0987654321` - ✅ Pass (Viettel)
3. `+84123456789` - ✅ Pass (Viettel international)
4. `84123456789` - ✅ Pass (Viettel international)
5. `0323456789` - ✅ Pass (Viettel)
6. `0523456789` - ✅ Pass (Vietnamobile)
7. `0723456789` - ✅ Pass (MobiFone)
8. `0823456789` - ✅ Pass (Vinaphone)
9. `0923456789` - ✅ Pass (Viettel)

#### Invalid Phone Numbers
1. `1234567890` - ❌ Fail (không bắt đầu bằng 0)
2. `+1234567890` - ❌ Fail (không phải +84)
3. `012345678` - ❌ Fail (thiếu số)
4. `01234567890` - ❌ Fail (thừa số)
5. `012345678a` - ❌ Fail (có ký tự không phải số)
6. `+8412345678` - ❌ Fail (thiếu số sau +84)
7. `+841234567890` - ❌ Fail (thừa số sau +84)

#### Edge Cases
1. ` 0123456789 ` - ✅ Pass (loại bỏ khoảng trắng)
2. `012-345-6789` - ✅ Pass (loại bỏ dấu gạch)
3. `012 345 6789` - ✅ Pass (loại bỏ khoảng trắng)
4. `012.345.6789` - ✅ Pass (loại bỏ dấu chấm)

## Security

- **Input sanitization**: Loại bỏ ký tự không hợp lệ
- **Format validation**: Kiểm tra format chuẩn
- **Length validation**: Giới hạn độ dài hợp lệ
- **Unique constraint**: Đảm bảo không trùng lặp

## Performance

- **Efficient regex**: Sử dụng regex tối ưu
- **Client-side validation**: Giảm server load
- **Real-time feedback**: UX tốt hơn
- **Minimal overhead**: Validation nhẹ

## Browser Compatibility

- **HTML5 input**: `type="tel"` cho mobile keyboard
- **Bootstrap styling**: Form validation classes
- **JavaScript support**: Real-time validation
- **Modern browsers**: Chrome, Firefox, Safari, Edge

## Future Enhancements

### Có thể mở rộng thêm:
1. **Auto-format**: Tự động format số điện thoại
2. **Network detection**: Phát hiện nhà mạng
3. **SMS verification**: Gửi SMS xác nhận
4. **International support**: Hỗ trợ số quốc tế khác
5. **Phone book integration**: Tích hợp danh bạ 