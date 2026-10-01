document.addEventListener('DOMContentLoaded', function () {
    const petList = document.querySelector('.pet-list');
    const calendarDays = document.getElementById('calendar-days');
    const calendarMonth = document.getElementById('calendar-month');
    const timeOptions = document.getElementById('time-options');
    const availabilityMessage = document.getElementById('availability-message');
    const summaryPetName = document.getElementById('summary-pet-name');
    const summaryPetAvatar = document.getElementById('summary-pet-avatar');
    const summaryDateTime = document.getElementById('summary-date-time');
    const summaryPrice = document.getElementById('summary-price');
    const summaryWeightRate = document.getElementById('summary-weight-rate');
    const addPetForm = document.getElementById('add-pet-form');
    const bookingPage = document.querySelector('.booking-page');
    const dateTimeLink = document.getElementById('to-date');
    const backToPetLink = document.getElementById('back-to-pet');
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const initialDateParts = bookingPage.dataset.initialDate.split('-').map(Number);
    let selectedDate = new Date(initialDateParts[0], initialDateParts[1] - 1, initialDateParts[2]);
    let monthToShow = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
    let selectedTime = bookingPage.dataset.initialTime || '';
    const bookingCode = 'PH-' + formatDate(selectedDate).replace(/-/g, '') + '-' + String(Math.floor(Math.random() * 900) + 100);

    function getWeightPrice(weight) {
        if (weight > 15) return { size: 'L', label: 'มากกว่า 15 กก.' };
        if (weight > 5) return { size: 'M', label: 'มากกว่า 5–15 กก.' };
        return { size: 'S', label: 'ไม่เกิน 5 กก.' };
    }

    function updatePetPrice(card) {
        const weight = Number(card.dataset.petWeight) || 0;
        const tier = getWeightPrice(weight);
        const price = Number(bookingPage.dataset['price' + tier.size]);
        summaryPrice.textContent = Math.round(price).toLocaleString('th-TH') + '฿';
        summaryWeightRate.textContent = 'น้ำหนัก ' + weight + ' กก. · ไซซ์ ' + tier.size + ' (' + tier.label + ')';
    }

    function formatDate(date) {
        return [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), String(date.getDate()).padStart(2, '0')].join('-');
    }

    function selectedPetCard() {
        return petList.querySelector('.pet-option.selected');
    }

    function bookingDateLabel() {
        return selectedDate ? selectedDate.toLocaleDateString('th-TH', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        }) : 'ยังไม่ได้เลือกวันเวลา';
    }

    function refreshReviewDetails() {
        const pet = selectedPetCard();
        if (!pet) return;

        const petName = pet.dataset.petName;
        const petBreed = pet.dataset.petBreed;
        const petType = pet.dataset.petType;
        const petDescription = petType + ' · ' + petBreed + ' · ' + pet.dataset.petWeight + ' กก.';
        const serviceName = document.querySelector('.summary-service strong').textContent;
        const dateTime = bookingDateLabel();
        const timeLabel = selectedTime ? Number(selectedTime.slice(0, 2)) + ':00 น.' : 'ยังไม่ได้เลือกเวลา';
        const price = summaryPrice.textContent;
        const isCat = petType === 'แมว';
        const avatar = isCat ? '🐱' : '🐶';

        document.getElementById('confirm-pet-name').textContent = petName;
        document.getElementById('confirm-pet-service').textContent = petDescription + ' · ' + serviceName + ' (เริ่มต้น ' + price + ')';
        document.getElementById('confirm-date').textContent = dateTime;
        document.getElementById('confirm-time').textContent = 'เวลา ' + timeLabel;
        document.getElementById('confirm-pet-avatar').textContent = avatar;
        document.getElementById('confirm-pet-avatar').className = 'pet-avatar ' + (isCat ? 'cat' : 'dog');

        document.getElementById('payment-booking-id').textContent = bookingCode;
        document.getElementById('payment-pet-name').textContent = petName + ' (' + petBreed + ')';
        document.getElementById('payment-pet-avatar').textContent = avatar;
        document.getElementById('payment-service').textContent = serviceName;
        document.getElementById('payment-date').textContent = dateTime;
        document.getElementById('payment-time').textContent = timeLabel;
        document.getElementById('payment-total').textContent = price;
        document.getElementById('pay-demo').textContent = 'จำลองการชำระเงิน (' + price + ')';
    }

    function selectPet(card) {
        petList.querySelectorAll('.pet-option').forEach(function (item) {
            item.classList.remove('selected');
            item.querySelector('input').checked = false;
        });

        card.classList.add('selected');
        card.querySelector('input').checked = true;
        const petId = card.querySelector('input').value;
        const dateTimeUrl = new URL(bookingPage.dataset.dateTimeUrl, window.location.origin);
        dateTimeUrl.searchParams.set('pet_id', petId);
        dateTimeLink.href = dateTimeUrl.pathname + dateTimeUrl.search;
        const petUrl = new URL(bookingPage.dataset.petUrl, window.location.origin);
        petUrl.searchParams.set('pet_id', petId);
        backToPetLink.href = petUrl.pathname + petUrl.search;
        summaryPetName.textContent = card.dataset.petName + ' · ' + card.dataset.petBreed;
        updatePetPrice(card);
        const isCat = card.dataset.petType === 'แมว';
        summaryPetAvatar.textContent = isCat ? '🐱' : '🐶';
        summaryPetAvatar.className = 'pet-avatar ' + (isCat ? 'cat' : 'dog');
    }

    function createPetCard(pet) {
        const isCat = pet.type === 'แมว';
        const card = document.createElement('label');
        card.className = 'pet-option';
        card.dataset.petName = pet.name;
        card.dataset.petBreed = pet.breed;
        card.dataset.petType = pet.type;
        card.dataset.petWeight = pet.weight;

        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'pet';
        radio.value = String(Date.now());

        const avatar = document.createElement('span');
        avatar.className = 'pet-avatar ' + (isCat ? 'cat' : 'dog');
        avatar.setAttribute('aria-hidden', 'true');
        avatar.textContent = isCat ? '🐱' : '🐶';

        const info = document.createElement('span');
        info.className = 'pet-info';
        const name = document.createElement('strong');
        name.textContent = pet.name;
        const details = document.createElement('small');
        details.textContent = pet.type + ' · ' + pet.breed + ' · ' + pet.age + ' ปี · น้ำหนัก ' + pet.weight + ' กก.';
        info.append(name, details);

        const mark = document.createElement('span');
        mark.className = 'radio-mark';
        mark.setAttribute('aria-hidden', 'true');
        mark.textContent = '✓';

        card.append(radio, avatar, info, mark);
        petList.appendChild(card);
        selectPet(card);
    }

    function updateDateTimeSummary() {
        if (!selectedDate) {
            summaryDateTime.textContent = 'ยังไม่ได้เลือกวันเวลา';
            document.getElementById('to-confirm').disabled = true;
            return;
        }

        const dateLabel = bookingDateLabel();
        if (!selectedTime) {
            summaryDateTime.textContent = dateLabel + ' · ยังไม่ได้เลือกเวลา';
            document.getElementById('to-confirm').disabled = true;
            return;
        }

        summaryDateTime.textContent = dateLabel + ' · ' + Number(selectedTime.slice(0, 2)) + ':00 น.';
        document.getElementById('to-confirm').disabled = false;
    }

    function renderCalendar() {
        const year = monthToShow.getFullYear();
        const month = monthToShow.getMonth();
        calendarMonth.textContent = monthToShow.toLocaleDateString('th-TH', { month: 'long' }) + ' ' + (year + 543);
        calendarDays.replaceChildren();

        const firstWeekday = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const totalCells = Math.ceil((firstWeekday + daysInMonth) / 7) * 7;

        for (let index = 0; index < totalCells; index += 1) {
            const date = new Date(year, month, index - firstWeekday + 1);
            const isCurrentMonth = date.getMonth() === month;
            const dayButton = document.createElement('button');
            dayButton.type = 'button';
            dayButton.className = 'calendar-day';
            dayButton.textContent = String(date.getDate());
            dayButton.dataset.date = formatDate(date);
            dayButton.setAttribute('aria-label', date.toLocaleDateString('th-TH', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            }));

            if (!isCurrentMonth) {
                dayButton.classList.add('outside-month');
                dayButton.disabled = true;
            }
            if (date < today) {
                dayButton.disabled = true;
                dayButton.classList.add('past-date');
            }
            if (formatDate(date) === formatDate(today)) {
                dayButton.classList.add('today');
            }
            if (selectedDate && formatDate(date) === formatDate(selectedDate)) {
                dayButton.classList.add('selected');
                dayButton.setAttribute('aria-pressed', 'true');
            } else {
                dayButton.setAttribute('aria-pressed', 'false');
            }

            calendarDays.appendChild(dayButton);
        }

        document.getElementById('previous-month').disabled =
            year < today.getFullYear() || (year === today.getFullYear() && month <= today.getMonth());
    }

    function renderSampleTimes() {
        timeOptions.replaceChildren();
        if (!selectedDate) {
            availabilityMessage.textContent = 'เลือกวันที่ก่อน แล้วเลือกเวลาที่ต้องการ';
            return;
        }

        availabilityMessage.textContent = 'เวลาให้เลือกเป็นข้อมูลตัวอย่าง ยังไม่ได้ตรวจสอบเวลาว่างจริง';
        const sampleTimes = [
            { value: '09:00', available: true },
            { value: '10:00', available: true },
            { value: '13:00', available: true },
            { value: '14:00', available: false },
            { value: '16:00', available: true },
            { value: '17:00', available: false },
        ];

        sampleTimes.forEach(function (slot) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'time-option';
            button.dataset.time = slot.value;
            button.disabled = !slot.available;
            button.textContent = Number(slot.value.slice(0, 2)) + ':00 น.' + (slot.available ? '' : ' (เต็มแล้ว)');
            if (slot.value === selectedTime) button.classList.add('chosen');
            timeOptions.appendChild(button);
        });
    }

    petList.addEventListener('change', function (event) {
        if (event.target.matches('input[name="pet"]')) {
            selectPet(event.target.closest('.pet-option'));
        }
    });

    document.querySelectorAll('input[name="pet_type"]').forEach(function (input) {
        input.addEventListener('change', function () {
            document.querySelectorAll('.type-option').forEach(function (option) {
                option.classList.toggle('chosen', option.querySelector('input').checked);
            });
        });
    });

    addPetForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const typeInput = addPetForm.querySelector('input[name="pet_type"]:checked');
        const name = document.getElementById('pet-name').value.trim();
        const breed = document.getElementById('pet-breed').value.trim();
        const age = document.getElementById('pet-age').value;
        const weight = document.getElementById('pet-weight').value;
        const gender = addPetForm.querySelector('select[name="pet_gender"]').value;

        if (!typeInput || !name || !breed || age === '' || !weight || !gender) {
            addPetForm.reportValidity();
            return;
        }

        createPetCard({ type: typeInput.value, name: name, breed: breed, age: age, weight: weight });
        addPetForm.reset();
        addPetForm.querySelector('input[name="pet_type"][value="สุนัข"]').checked = true;
        document.querySelectorAll('.type-option').forEach(function (option) {
            option.classList.toggle('chosen', option.querySelector('input').checked);
        });
        document.getElementById('demo-confirm-message').textContent = 'เพิ่มสัตว์เลี้ยงไว้ดูตัวอย่างในหน้านี้แล้ว ข้อมูลยังไม่ได้บันทึก';
    });

    calendarDays.addEventListener('click', function (event) {
        const button = event.target.closest('.calendar-day');
        if (!button || button.disabled) return;

        const [year, month, day] = button.dataset.date.split('-').map(Number);
        selectedDate = new Date(year, month - 1, day);
        selectedTime = '';
        renderCalendar();
        renderSampleTimes();
        updateDateTimeSummary();
    });

    document.getElementById('previous-month').addEventListener('click', function () {
        monthToShow = new Date(monthToShow.getFullYear(), monthToShow.getMonth() - 1, 1);
        renderCalendar();
    });
    document.getElementById('next-month').addEventListener('click', function () {
        monthToShow = new Date(monthToShow.getFullYear(), monthToShow.getMonth() + 1, 1);
        renderCalendar();
    });

    timeOptions.addEventListener('click', function (event) {
        const button = event.target.closest('.time-option');
        if (!button || button.disabled) return;

        selectedTime = button.dataset.time;
        timeOptions.querySelectorAll('.time-option').forEach(function (item) {
            item.classList.toggle('chosen', item === button);
        });
        updateDateTimeSummary();
    });

    function setStep(step) {
        if (step >= 2 && !petList.querySelector('input[name="pet"]:checked')) {
            alert('กรุณาเพิ่มหรือเลือกสัตว์เลี้ยงก่อนค่ะ');
            return;
        }

        if (step === 2 && !selectedDate) {
            selectedDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1);
            monthToShow = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
            renderCalendar();
            renderSampleTimes();
            updateDateTimeSummary();
        }

        if (step === 3 && (!selectedDate || !selectedTime)) {
            alert('กรุณาเลือกวันและเวลาตัวอย่างก่อนค่ะ');
            return;
        }

        document.getElementById('pet-panel').hidden = step !== 1;
        document.getElementById('add-pet-panel').hidden = step !== 1;
        document.getElementById('date-panel').hidden = step !== 2;
        document.getElementById('time-panel').hidden = step !== 2;
        document.getElementById('confirm-panel').hidden = step !== 3;
        document.getElementById('payment-panel').hidden = step !== 4;
        document.getElementById('success-panel').hidden = step !== 5;
        document.querySelector('.steps').hidden = step >= 4;
        bookingPage.dataset.screen = step === 4 ? 'payment' : (step === 5 ? 'success' : 'booking');
        document.getElementById('to-date').hidden = step !== 1;
        document.getElementById('date-summary-actions').hidden = step !== 2;
        document.getElementById('confirm-summary-actions').hidden = step !== 3;
        document.querySelector('.summary-panel').hidden = step >= 4;

        document.querySelectorAll('.step').forEach(function (button) {
            const targetStep = Number(button.dataset.stepTarget);
            button.classList.toggle('active', targetStep === step);
            button.classList.toggle('done', targetStep < step);
        });

        if (step === 3 || step === 4) {
            refreshReviewDetails();
        }

        if (step === 5) {
            refreshReviewDetails();
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const methodNames = { promptpay: 'QR Code พร้อมเพย์ (ตัวอย่าง)', card: 'บัตรเครดิต/เดบิต (ตัวอย่าง)', transfer: 'โอนผ่านธนาคาร (ตัวอย่าง)' };
            document.getElementById('success-booking-id').textContent = bookingCode;
            document.getElementById('success-pet').textContent = summaryPetName.textContent;
            document.getElementById('success-service').textContent = document.querySelector('.summary-service strong').textContent;
            document.getElementById('success-date-time').textContent = summaryDateTime.textContent;
            document.getElementById('success-payment-method').textContent = methodNames[selectedMethod];
            document.getElementById('success-total').textContent = summaryPrice.textContent;
        }
    }

    document.querySelectorAll('.step').forEach(function (button) {
        button.addEventListener('click', function () {
            setStep(Number(button.dataset.stepTarget));
        });
    });

    document.getElementById('back-to-pet').addEventListener('click', function () { setStep(1); });
    document.getElementById('to-confirm').addEventListener('click', function () { setStep(3); });
    document.getElementById('back-to-date').addEventListener('click', function () { setStep(2); });
    document.getElementById('confirm-and-pay').addEventListener('click', function () {
        const contactForm = document.getElementById('contact-form');
        if (!contactForm.reportValidity()) return;
        setStep(4);
    });
    document.getElementById('back-to-confirm').addEventListener('click', function () { setStep(3); });
    document.getElementById('pay-demo').addEventListener('click', function () {
        setStep(5);
    });
    document.querySelectorAll('input[name="payment_method"]').forEach(function (input) {
        input.addEventListener('change', function () {
            document.querySelectorAll('.payment-method').forEach(function (method) {
                method.classList.toggle('selected', method.querySelector('input').checked);
            });
            document.getElementById('promptpay-extra').hidden = input.value !== 'promptpay';
            document.getElementById('card-extra').hidden = input.value !== 'card';
            document.getElementById('transfer-extra').hidden = input.value !== 'transfer';
            refreshReviewDetails();
        });
    });

    const initiallySelectedPet = petList.querySelector('.pet-option.selected');
    if (initiallySelectedPet) selectPet(initiallySelectedPet);
    renderCalendar();
    renderSampleTimes();
    const initialStep = Number(bookingPage.dataset.initialStep || 1);
    setStep(initialStep);
    if (initialStep === 2) updateDateTimeSummary();
});
