<?php

return [
    'created' => 'تم إنشاء :resource بنجاح.',
    'updated' => 'تم تحديث :resource بنجاح.',
    'deleted' => 'تم حذف :resource بنجاح.',
    'not_found' => ':resource غير موجود.',
    'forbidden' => 'غير مصرح لك بتنفيذ هذا الإجراء.',
    'unauthenticated' => 'يرجى تسجيل الدخول للمتابعة.',
    'success' => 'تمت العملية بنجاح.',
    'validation_failed' => 'فشل التحقق من البيانات.',
    'rider' => [
        'created' => 'تم إنشاء حساب الراكب بنجاح',
        'updated' => 'تم تحديث حساب الراكب بنجاح',
        'deleted' => 'تم حذف حساب الراكب بنجاح',
        'retrieved' => 'تم استرجاع حساب الراكب بنجاح',
        'list' => 'تم استرجاع حسابات الركاب بنجاح',
        'completion' => 'تم إكمال بيانات الراكب بنجاح',
        'error' => ['profile_already_completed' => 'تم إكمال الملف الشخصي مسبقاً',    'profile_incomplete' => 'يرجى إكمال ملفك الشخصي قبل الوصول إلى هذه الميزة',],
    ],
    'rider_location_folder' => [
        // Success Messages
        'created' => 'تم إنشاء مجلد مواقع الركاب بنجاح',
        'updated' => 'تم تحديث مجلد مواقع الركاب بنجاح',
        'deleted' => 'تم حذف مجلد مواقع الركاب بنجاح',
        'retrieved' => 'تم استرجاع مجلد مواقع الركاب بنجاح',
        'list' => 'تم استرجاع قائمة مجلدات مواقع الركاب بنجاح',
        // Error Messages
        'error' => [
            'creation_failed' => 'فشل إنشاء مجلد المواقع',
            'update_failed' => 'فشل تحديث مجلد المواقع',
            'delete_failed' => 'فشل حذف مجلد المواقع',
            'not_found' => 'مجلد المواقع غير موجود',
        ]
    ],
    'rider_saved_location' => [
        // Success Messages
        'created' => 'تم حفظ الموقع بنجاح',
        'updated' => 'تم تحديث الموقع المحفوظ بنجاح',
        'deleted' => 'تم حذف الموقع المحفوظ بنجاح',
        'retrieved' => 'تم استرجاع الموقع المحفوظ بنجاح',
        'list' => 'تم استرجاع قائمة المواقع المحفوظة بنجاح',
        // Error Messages
        'error' => [
            'creation_failed' => 'فشل حفظ الموقع',
            'update_failed' => 'فشل تحديث الموقع المحفوظ',
            'delete_failed' => 'فشل حذف الموقع المحفوظ',
            'not_found' => 'الموقع المحفوظ غير موجود',
            'limit_reached' => 'تم الوصول للحد الأقصى للمواقع المحفوظة',
            'invalid_alias' => 'اسم تعريف الموقع غير صالح',
            'invalid_coordinates' => 'إحداثيات الموقع غير صالحة',
            'duplicate_location' => 'الموقع مسجل بالفعل',
            'sharing_failed' => 'فشل مشاركة الموقع',
            'privacy_restriction' => 'غير مسموح بمشاركة هذا الموقع',
            'geofence_conflict' => 'الموقع خارج النطاق المسموح',
            'address_resolve_failed' => 'فشل تحديد عنوان الموقع',
            'type_conflict' => 'يوجد بالفعل موقع من هذا النوع (منزل/عمل)',
            'favorite_limit' => 'تم الوصول للحد الأقصى للمواقع المفضلة'
        ]
    ],
    'country' => [
        'created' => 'تم إنشاء الدولة بنجاح',
        'updated' => 'تم تحديث الدولة بنجاح',
        'deleted' => 'تم حذف الدولة بنجاح',
        'retrieved' => 'تم استرجاع الدولة بنجاح',
        'list' => 'تم استرجاع الدولة بنجاح',
        'error' => [
            'activation_failed' => 'فشل تفعيل الدولة',
            'deactivation_failed' => 'فشل تعطيل الدولة',
        ]
    ],
    'coupon' => [
        'created' => 'تم إنشاء الكوبون بنجاح',
        'updated' => 'تم تحديث الكوبون بنجاح',
        'deleted' => 'تم حذف الكوبون بنجاح',
        'retrieved' => 'تم استرجاع الكوبون بنجاح',
        'list' => 'تم استرجاع قائمة الكوبونات بنجاح',
        'applied' => 'تم تطبيق الكوبون بنجاح',
        'expired' => 'انتهت صلاحية الكوبون',
        'invalid' => 'كوبون غير صالح',
        'activated' => 'تم تفعيل الكوبون بنجاح',
        'deactivated' => 'تم إيقاف الكوبون بنجاح',
        'error' => [
            'activation_failed' => 'فشل تفعيل الكوبون',
            'deactivation_failed' => 'فشل إيقاف الكوبون',
        ],
    ],
    'rider_coupon' => [
        // Success Messages
        'created' => 'تم إنشاء كوبون الراكب بنجاح',
        'updated' => 'تم تحديث كوبون الراكب بنجاح',
        'deleted' => 'تم حذف كوبون الراكب بنجاح',
        'retrieved' => 'تم استرجاع كوبون الراكب بنجاح',
        'list' => 'تم استرجاع قائمة كوبونات الركاب بنجاح',
        // Error Messages
        'error' => [
            'creation_failed' => 'فشل إنشاء كوبون الراكب',
            'update_failed' => 'فشل تحديث كوبون الراكب',
            'delete_failed' => 'فشل حذف كوبون الراكب',
            'not_found' => 'كوبون الراكب غير موجود',
        ]
    ],
    'car_manufacture' => [
        'created' => 'تم إنشاء شركة تصنيع السيارات بنجاح',
        'updated' => 'تم تحديث شركة تصنيع السيارات بنجاح',
        'deleted' => 'تم حذف شركة تصنيع السيارات بنجاح',
        'retrieved' => 'تم استرجاع شركة تصنيع السيارات بنجاح',
        'list' => 'تم استرجاع قائمة شركات تصنيع السيارات بنجاح',
        'activated' => 'تم تفعيل شركة التصنيع بنجاح',
        'deactivated' => 'تم إيقاف شركة التصنيع بنجاح',
        'error' => [
            'activation_failed' => 'فشل تفعيل شركة التصنيع',
            'deactivation_failed' => 'فشل إيقاف شركة التصنيع',
        ],
    ],
    'car_model' => [
        'created' => 'تم إنشاء موديل السيارة بنجاح',
        'updated' => 'تم تحديث موديل السيارة بنجاح',
        'deleted' => 'تم حذف موديل السيارة بنجاح',
        'retrieved' => 'تم استرجاع موديل السيارة بنجاح',
        'list' => 'تم استرجاع قائمة موديلات السيارات بنجاح',
        'activated' => 'تم تفعيل موديل السيارة بنجاح',
        'deactivated' => 'تم إيقاف موديل السيارة بنجاح',
        'error' => [
            'activation_failed' => 'فشل تفعيل موديل السيارة',
            'deactivation_failed' => 'فشل إيقاف موديل السيارة',
        ]
    ],
    'car_service_level' => [
        'created' => 'تم إنشاء مستوى خدمة السيارة بنجاح',
        'updated' => 'تم تحديث مستوى خدمة السيارة بنجاح',
        'deleted' => 'تم حذف مستوى خدمة السيارة بنجاح',
        'retrieved' => 'تم استرجاع مستوى خدمة السيارة بنجاح',
        'list' => 'تم استرجاع قائمة مستويات خدمة السيارات بنجاح',
        'activated' => 'تم تفعيل مستوى الخدمة بنجاح',
        'deactivated' => 'تم إيقاف مستوى الخدمة بنجاح',
        'error' => [
            'activation_failed' => 'فشل تفعيل مستوى الخدمة',
            'deactivation_failed' => 'فشل إيقاف مستوى الخدمة',
        ]
    ],
    'payment_method' => [
        // Success Messages
        'created' => 'تم إنشاء طريقة الدفع بنجاح',
        'updated' => 'تم تحديث طريقة الدفع بنجاح',
        'deleted' => 'تم حذف طريقة الدفع بنجاح',
        'retrieved' => 'تم استرجاع طريقة الدفع بنجاح',
        'list' => 'تم استرجاع قائمة طرق الدفع بنجاح',
        'activated' => 'تم تفعيل طريقة الدفع بنجاح',
        'deactivated' => 'تم تعطيل طريقة الدفع بنجاح',
        'default_set' => 'تم تعيين طريقة الدفع الافتراضية بنجاح',

        // Error Messages
        'error' => [
            'activation_failed' => 'فشل تفعيل طريقة الدفع',
            'deactivation_failed' => 'فشل تعطيل طريقة الدفع',
        ]
    ],
    'driver_status' => [
        'created' => 'تم إنشاء حالة السائق بنجاح',
        'updated' => 'تم تحديث حالة السائق بنجاح',
        'deleted' => 'تم حذف حالة السائق بنجاح',
        'retrieved' => 'تم استرجاع حالة السائق بنجاح',
        'list' => 'تم استرجاع قائمة حالات السائقين بنجاح',
        'not_found' => 'حالة السائق غير موجودة',
        'exists' => 'حالة السائق موجودة بالفعل',
        'in_use' => 'لا يمكن الحذف، الحالة مستخدمة حالياً',
        'status_changed' => 'تم تغيير حالة السائق بنجاح',
        'default_cannot_delete' => 'لا يمكن حذف الحالة الافتراضية',
    ],
    'pricing' => [
        // Success Messages
        'created' => 'تم إنشاء التسعير بنجاح',
        'updated' => 'تم تحديث التسعير بنجاح',
        'deleted' => 'تم حذف التسعير بنجاح',
        'retrieved' => 'تم استرجاع التسعير بنجاح',
        'list' => 'تم استرجاع قائمة الأسعار بنجاح',
        'activated' => 'تم تفعيل خطة التسعير بنجاح',
        'deactivated' => 'تم تعطيل خطة التسعير بنجاح',
        // Error Messages
        'error' => [
            'activation_failed' => 'فشل تفعيل خطة التسعير',
            'deactivation_failed' => 'فشل تعطيل خطة التسعير',
        ]
    ],
    'trip_type' => [
        // Success Messages
        'created' => 'تم إنشاء نوع الرحلة بنجاح',
        'updated' => 'تم تحديث نوع الرحلة بنجاح',
        'deleted' => 'تم حذف نوع الرحلة بنجاح',
        'retrieved' => 'تم استرجاع نوع الرحلة بنجاح',
        'list' => 'تم استرجاع قائمة أنواع الرحلات بنجاح',
        'activated' => 'تم تفعيل نوع الرحلة بنجاح',
        'deactivated' => 'تم تعطيل نوع الرحلة بنجاح',

        // Error Messages
        'error' => [
            'creation_failed' => 'فشل إنشاء نوع الرحلة',
            'update_failed' => 'فشل تحديث نوع الرحلة',
            'delete_failed' => 'فشل حذف نوع الرحلة',
            'not_found' => 'نوع الرحلة غير موجود',
            'activation_failed' => 'فشل تفعيل نوع الرحلة',
            'deactivation_failed' => 'فشل تعطيل نوع الرحلة',
        ]
    ],
    'trip_time_type' => [
        // Success Messages
        'created' => 'تم إنشاء نوع وقت الرحلة بنجاح',
        'updated' => 'تم تحديث نوع وقت الرحلة بنجاح',
        'deleted' => 'تم حذف نوع وقت الرحلة بنجاح',
        'retrieved' => 'تم استرجاع نوع وقت الرحلة بنجاح',
        'list' => 'تم استرجاع قائمة أنواع أوقات الرحلات بنجاح',

        // Error Messages
        'error' => [
            'creation_failed' => 'فشل إنشاء نوع وقت الرحلة',
            'update_failed' => 'فشل تحديث نوع وقت الرحلة',
            'delete_failed' => 'فشل حذف نوع وقت الرحلة',
            'not_found' => 'نوع وقت الرحلة غير موجود',
            'activation_failed' => 'فشل تفعيل نوع وقت الرحلة',
            'deactivation_failed' => 'فشل تعطيل نوع وقت الرحلة',
        ]
    ],
    'trip' => [
        // Success Messages
        'created' => 'تم إنشاء الرحلة بنجاح',
        'updated' => 'تم تحديث الرحلة بنجاح',
        'deleted' => 'تم حذف الرحلة بنجاح',
        'retrieved' => 'تم استرجاع الرحلة بنجاح',
        'list' => 'تم استرجاع قائمة الرحلات بنجاح',
        'started' => 'تم بدء الرحلة بنجاح',
        'completed' => 'تم إكمال الرحلة بنجاح',
        'canceled' => 'تم إلغاء الرحلة بنجاح',
        'driver_assigned' => 'تم تعيين سائق للرحلة بنجاح',
        'driver_arrived' => 'وصل السائق إلى نقطة الالتقاط',
        'rider_picked_up' => 'تم اصطحاب الراكب بنجاح',
        'route_updated' => 'تم تحديث مسار الرحلة بنجاح',
        'payment_processed' => 'تمت معالجة الدفع بنجاح',
        'rated' => 'تم تقييم الرحلة بنجاح',
        'scheduled' => 'تم جدولة الرحلة بنجاح',

        // Error Messages
        'error' => [
            'creation_failed' => 'فشل إنشاء الرحلة',
            'update_failed' => 'فشل تحديث الرحلة',
            'delete_failed' => 'فشل حذف الرحلة',
            'not_found' => 'الرحلة غير موجودة',
            'start_failed' => 'فشل بدء الرحلة',
            'completion_failed' => 'فشل إكمال الرحلة',
            'cancellation_failed' => 'فشل إلغاء الرحلة',
            'invalid_status' => 'حالة الرحلة غير صالحة',
            'driver_unavailable' => 'لا يوجد سائق متاح',
            'driver_assignment_failed' => 'فشل تعيين سائق',
            'rider_not_ready' => 'الراكب غير جاهز للركوب',
            'pickup_failed' => 'فشل عملية الالتقاط',
            'route_calculation_failed' => 'فشل حساب المسار',
            'payment_failed' => 'فشل معالجة الدفع',
            'rating_failed' => 'فشل تقييم الرحلة',
            'scheduling_failed' => 'فشل جدولة الرحلة',
            'timeout' => 'انتهى وقت الانتظار',
            'distance_exceeded' => 'المسافة المقطوعة تجاوزت المخطط',
            'price_mismatch' => 'عدم تطابق السعر النهائي مع التقدير',
            'vehicle_mismatch' => 'نوع المركبة غير مطابق',
            'rider_cancellation_fee' => 'تم تطبيق رسوم إلغاء الرحلة',
            'driver_cancellation_penalty' => 'تم تطبيق عقوبة إلغاء السائق',
            'no_drivers_available' => 'لا يوجد سائقون متاحون ضمن نطاق البحث. يرجى المحاولة مرة أخرى.',
        ]
    ],
    'trip_location' => [
        // Success Messages
        'created' => 'تم إنشاء موقع الرحلة بنجاح',
        'updated' => 'تم تحديث موقع الرحلة بنجاح',
        'deleted' => 'تم حذف موقع الرحلة بنجاح',
        'retrieved' => 'تم استرجاع موقع الرحلة بنجاح',
        'list' => 'تم استرجاع قائمة مواقع الرحلة بنجاح',
        'completed' => 'تم تأكيد الوصول إلى الموقع بنجاح',
        'sequence_updated' => 'تم تحديث تسلسل المواقع بنجاح',
        'eta_updated' => 'تم تحديث وقت الوصول المتوقع بنجاح',
        'arrival_confirmed' => 'تم تسجيل وقت الوصول الفعلي بنجاح',
        'route_optimized' => 'تم تحسين مسار المواقع بنجاح',

        // Error Messages
        'error' => [
            'creation_failed' => 'فشل إنشاء موقع الرحلة',
            'update_failed' => 'فشل تحديث موقع الرحلة',
            'delete_failed' => 'فشل حذف موقع الرحلة',
            'not_found' => 'موقع الرحلة غير موجود',
            'completion_failed' => 'فشل تأكيد الوصول إلى الموقع',
            'invalid_sequence' => 'تسلسل المواقع غير صالح',
            'eta_calculation_failed' => 'فشل حساب وقت الوصول المتوقع',
            'arrival_confirmation_failed' => 'فشل تسجيل وقت الوصول الفعلي',
            'route_optimization_failed' => 'فشل تحسين مسار المواقع',
            'invalid_location_type' => 'نوع الموقع غير صالح',
            'order_conflict' => 'تعارض في ترتيب المواقع',
            'trip_completed' => 'لا يمكن التعديل على رحلة مكتملة',
            'invalid_coordinates' => 'إحداثيات الموقع غير صالحة',
            'distance_calculation_failed' => 'فشل حساب المسافة بين المواقع',
            'sequence_out_of_bounds' => 'ترتيب الموقع خارج النطاق المسموح',
            'already_completed' => 'الموقع مكتمل بالفعل',
            'dependency_exists' => 'لا يمكن الحذف بسبب وجود مواقع تابعة',
        ]
    ],
    'trip_status' => [
        // Success Messages
        'created' => 'تم إنشاء حالة الرحلة بنجاح',
        'updated' => 'تم تحديث حالة الرحلة بنجاح',
        'deleted' => 'تم حذف حالة الرحلة بنجاح',
        'retrieved' => 'تم استرجاع حالة الرحلة بنجاح',
        'list' => 'تم استرجاع قائمة حالات الرحلات بنجاح',
        // Error Messages
        'error' => [
            'creation_failed' => 'فشل إنشاء حالة الرحلة',
            'update_failed' => 'فشل تحديث حالة الرحلة',
            'delete_failed' => 'فشل حذف حالة الرحلة',
            'not_found' => 'حالة الرحلة غير موجودة',
        ]
    ],
    'driver' => [
        'created' => 'تم إنشاء حساب السائق بنجاح',
        'updated' => 'تم تحديث حساب السائق بنجاح',
        'deleted' => 'تم حذف حساب السائق بنجاح',
        'retrieved' => 'تم استرجاع حساب السائق بنجاح',
        'list' => 'تم استرجاع حسابات السائقين بنجاح',
        'completion' => 'تم إكمال بيانات السائق بنجاح',
        'suspended' => 'تم تعليق حساب السائق بنجاح',
        'reinstate' => 'تم إعادة تفعيل حساب السائق بنجاح',
        'error' => [
            'suspension_failed' => 'فشل تعليق الحساب',
            'reinstate_failed' => 'فشل إعادة تفعيل الحساب',
            'profile_already_completed' => 'تم إكمال الملف الشخصي مسبقاً',
            'car_already_added' => 'تمت إضافة المركبة مسبقاً', // New message added here
            'profile_incomplete' => 'يرجى إكمال ملفك الشخصي قبل الوصول إلى هذه الميزة',
        ],
        'car' => [
            'created' => 'تم إنشاء المركبة بنجاح'
        ]
    ],
    'auth' => [
        'verification' => [
            'required' => 'البريد الإلكتروني غير مفعل. تم إرسال رسالة تحقق جديدة. يرجى التحقق من بريدك.',
            'resent' => 'تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني.',
            'sent' => 'تم إرسال رمز التحقق'
        ],
        'registered' => 'تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني.',
        'logout' => 'تم تسجيل الخروج بنجاح',
        'profile' => [
            'retrieved' => 'تم استرجاع بيانات الملف الشخصي بنجاح',
            'updated' => 'تم تحديث الملف الشخصي بنجاح',
        ],
        'error' => [
            'update_failed' => 'Failed to update profile',
        ]
    ],
    'error' => [
        'generic' => 'حدث خطأ ما. الرجاء المحاولة لاحقًا',
        'creation_failed' => 'فشل الإنشاء',
        'update_failed' => 'فشل التحديث',
        'delete_failed' => 'فشل الحذف',
    ],
];
