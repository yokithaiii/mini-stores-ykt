/**
 * Нормализует номер телефона к формату 7XXXXXXXXXX
 * Удаляет все символы кроме цифр и приводит к единому формату
 * 
 * @param {string} phone - Номер телефона в любом формате
 * @returns {string} - Нормализованный номер в формате 7XXXXXXXXXX
 */
export function normalizePhone(phone) {
    if (!phone) return '';
    
    // Удаляем все символы кроме цифр
    let normalized = phone.replace(/[^0-9]/g, '');
    
    // Если номер начинается с 8, заменяем на 7
    if (normalized.length === 11 && normalized.startsWith('8')) {
        normalized = '7' + normalized.substring(1);
    }
    
    // Если номер без кода страны (10 цифр), добавляем 7
    if (normalized.length === 10) {
        normalized = '7' + normalized;
    }
    
    // Если номер начинается с 77 (ошибка при вводе +7), убираем лишнюю 7
    if (normalized.length === 12 && normalized.startsWith('77')) {
        normalized = normalized.substring(1);
    }
    
    return normalized;
}

/**
 * Форматирует номер телефона для отображения
 * 7XXXXXXXXXX -> +7 (XXX) XXX-XX-XX
 * 
 * @param {string} phone - Нормализованный номер телефона
 * @returns {string} - Отформатированный номер
 */
export function formatPhone(phone) {
    const normalized = normalizePhone(phone);
    
    if (normalized.length === 11 && normalized.startsWith('7')) {
        return `+${normalized[0]} (${normalized.substring(1, 4)}) ${normalized.substring(4, 7)}-${normalized.substring(7, 9)}-${normalized.substring(9, 11)}`;
    }
    
    return phone;
}

/**
 * Проверяет валидность номера телефона
 * 
 * @param {string} phone - Номер телефона
 * @returns {boolean} - true если номер валиден
 */
export function isValidPhone(phone) {
    const normalized = normalizePhone(phone);
    return normalized.length === 11 && normalized.startsWith('7');
}
