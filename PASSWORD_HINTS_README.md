# Tính năng Gợi ý Mật khẩu

## Mô tả
Đã thêm các tính năng gợi ý và hỗ trợ cho việc tạo mật khẩu mạnh trong form thêm người dùng, giúp admin dễ dàng tạo mật khẩu đáp ứng yêu cầu bảo mật.

## Tính năng

### 1. Tooltip Gợi ý
- **Icon thông tin**: Hiển thị icon dấu hỏi bên cạnh label
- **Tooltip chi tiết**: Hiển thị yêu cầu mật khẩu khi hover
- **Vị trí thông minh**: Tooltip hiển thị ở vị trí phù hợp

### 2. Real-time Validation với Visual Feedback
- **Checklist trực quan**: Hiển thị danh sách yêu cầu với icon
- **Real-time update**: Cập nhật trạng thái ngay khi nhập
- **Color coding**: Xanh = đạt yêu cầu, Xám = chưa đạt

### 3. Ví dụ Mật khẩu Mạnh
- **Ví dụ cụ thể**: Hiển thị các mật khẩu mẫu
- **Format code**: Sử dụng thẻ `<code>` để highlight
- **Dễ copy**: Người dùng có thể copy làm mẫu

### 4. Tạo Mật khẩu Ngẫu nhiên
- **Button tạo**: Icon xúc xắc để tạo mật khẩu
- **Mật khẩu mạnh**: Đảm bảo đáp ứng tất cả yêu cầu
- **Auto-fill**: Tự động điền vào input field
- **Auto-show**: Tự động hiển thị mật khẩu

## Files đã thay đổi

### `resources/views/admin/users/index.blade.php`
- Thêm tooltip cho password, email, phone number fields
- Thêm button tạo mật khẩu ngẫu nhiên
- Thêm ví dụ mật khẩu mạnh
- Cập nhật JavaScript để xử lý tooltip và tạo mật khẩu

## UI Elements

### Tooltip Icons
```html
<i class="fas fa-question-circle text-info ms-1" 
   data-bs-toggle="tooltip" 
   data-bs-placement="top" 
   title="Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt (@$!%*?&)"></i>
```

### Password Requirements Checklist
```html
<ul class="mb-0 mt-1 ps-3">
    <li id="length-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 8 ký tự</li>
    <li id="lowercase-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 1 chữ thường</li>
    <li id="uppercase-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 1 chữ hoa</li>
    <li id="number-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 1 số</li>
    <li id="special-check" class="text-muted"><i class="fas fa-circle text-muted"></i> Ít nhất 1 ký tự đặc biệt (@$!%*?&)</li>
</ul>
```

### Generate Password Button
```html
<button class="btn btn-outline-info" type="button" id="generatePassword" title="Tạo mật khẩu ngẫu nhiên">
    <i class="fas fa-dice"></i>
</button>
```

### Password Examples
```html
<div class="mt-2">
    <strong>Ví dụ mật khẩu mạnh:</strong> <code>TLU@2024</code>, <code>FindIt@TLU</code>, <code>Student@123</code>
</div>
```

## JavaScript Features

### Tooltip Initialization
```javascript
// Khởi tạo tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
```

### Generate Strong Password
```javascript
function generateStrongPassword() {
    const lowercase = 'abcdefghijklmnopqrstuvwxyz';
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const numbers = '0123456789';
    const special = '@$!%*?&';
    
    let password = '';
    
    // Đảm bảo có ít nhất 1 ký tự từ mỗi loại
    password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
    password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
    password += numbers.charAt(Math.floor(Math.random() * numbers.length));
    password += special.charAt(Math.floor(Math.random() * special.length));
    
    // Thêm các ký tự ngẫu nhiên để đủ 8 ký tự
    const allChars = lowercase + uppercase + numbers + special;
    for (let i = 4; i < 12; i++) {
        password += allChars.charAt(Math.floor(Math.random() * allChars.length));
    }
    
    // Xáo trộn mật khẩu
    return password.split('').sort(() => Math.random() - 0.5).join('');
}
```

### Real-time Validation
```javascript
function validatePassword(password) {
    const checks = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        number: /\d/.test(password),
        special: /[@$!%*?&]/.test(password)
    };

    // Update UI for each check
    updateCheckUI('length-check', checks.length);
    updateCheckUI('lowercase-check', checks.lowercase);
    updateCheckUI('uppercase-check', checks.uppercase);
    updateCheckUI('number-check', checks.number);
    updateCheckUI('special-check', checks.special);
}
```

## Tooltip Messages

### Password Field
```
"Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt (@$!%*?&)"
```

### Email Field
```
"Chỉ chấp nhận email có đuôi @e.tlu.edu.vn hoặc @tlu.edu.vn"
```

### Phone Number Field
```
"Chỉ chấp nhận số điện thoại Việt Nam với đầu số: 03, 05, 07, 08, 09"
```

## Password Examples

### Ví dụ mật khẩu mạnh:
- `TLU@2024` - Kết hợp tên trường và năm
- `FindIt@TLU` - Kết hợp tên app và trường
- `Student@123` - Kết hợp vai trò và số

### Mật khẩu được tạo tự động:
- Độ dài: 12 ký tự
- Bao gồm: Chữ hoa, chữ thường, số, ký tự đặc biệt
- Ngẫu nhiên: Không thể đoán được
- An toàn: Đáp ứng tất cả yêu cầu bảo mật

## User Experience

### 1. Visual Guidance
- **Tooltip hints**: Hướng dẫn ngay khi hover
- **Real-time feedback**: Thấy ngay kết quả validation
- **Color indicators**: Dễ dàng nhận biết trạng thái

### 2. Convenience Features
- **Auto-generate**: Tạo mật khẩu mạnh với 1 click
- **Copy examples**: Có thể copy ví dụ làm mẫu
- **Show/hide**: Toggle hiển thị mật khẩu

### 3. Educational Value
- **Learn requirements**: Hiểu rõ yêu cầu mật khẩu
- **Best practices**: Thấy ví dụ mật khẩu tốt
- **Security awareness**: Nâng cao ý thức bảo mật

## Testing

### Test Cases

#### Tooltip Functionality
1. Hover vào icon dấu hỏi → Tooltip hiển thị
2. Tooltip có nội dung đúng và đầy đủ
3. Tooltip hiển thị ở vị trí phù hợp

#### Password Generation
1. Click button tạo mật khẩu → Mật khẩu được tạo
2. Mật khẩu đáp ứng tất cả yêu cầu
3. Tất cả checklist items chuyển sang màu xanh
4. Mật khẩu được hiển thị (không ẩn)

#### Real-time Validation
1. Nhập mật khẩu yếu → Checklist items màu xám
2. Nhập mật khẩu mạnh → Checklist items màu xanh
3. Validation update ngay lập tức

## Future Enhancements

### Có thể mở rộng thêm:
1. **Password strength meter**: Thanh đo độ mạnh mật khẩu
2. **Custom password patterns**: Tạo mật khẩu theo pattern
3. **Password history**: Lưu lịch sử mật khẩu đã tạo
4. **Export passwords**: Xuất danh sách mật khẩu
5. **Bulk generation**: Tạo nhiều mật khẩu cùng lúc 