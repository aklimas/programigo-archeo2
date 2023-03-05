INSERT INTO `email_template_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywny', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywny ', 'disabled', '#A00', NULL, NULL);


INSERT INTO `files` (`id`, `path`, `name`, `extension`, `title`, `alt`, `date_add`)
VALUES (1, '/defaultUpload/', 'programigo.png', 'png', NULL, NULL, '2022-08-17 13:23:36'),
       (2, '/defaultUpload/', 'programigo_icon.png', 'png', NULL, NULL, '2022-08-17 13:23:36');

INSERT INTO `settings` (`id`, `file_id`, `name`, `value`)
VALUES (1, NULL, 'serviceType', 'close'),
       (2, NULL, 'agreeAccount', 'Wyrażam zgodę na założenie konta w systemie.'),
       (3, NULL, 'agreeTerms',
        'Wyrażam zgodę na otrzymywanie drogą elektroniczną na wskazany przeze mnie adres e-mail informacji handlowej w rozumieniu art.10 ust.1 ustawy z dnia 18 lipca 2002 roku o świadczeniu usług drogą elektroniczną (Dz.U. 2002 Nr 144 poz. 1204 ze zmianami).'),
       (4, NULL, 'replacementMachine', 'Zgadzam się'),
       (5, NULL, 'pageTitle', 'Programigo'),
       (6, NULL, 'emailUserCreateSubject', 'Konto zostało założone'),
       (7, NULL, 'emailUserCreateContent',
        'Cześć [imie], \r\nTwoje dane logowania:\r\n[email]\r\n[haslo]\r\n\r\nZaloguj się poprzez link:\r\n[adres_logowania]'),
       (8, NULL, 'emailAdmin', 'kontakt@programigo.com'),
       (9, NULL, 'emailLabelAdmin', 'Administrator'),
       (10, NULL, 'accept1', 'Treść zgody'),
       (11, NULL, 'emailFooter', 'Podpis'),
       (12, 1, 'logo', ''),
       (13, 2, 'favicon', ''),
       (14, NULL, 'przelewy24_id', '107001'),
       (15, NULL, 'przelewy24_crc', 'e829a3b90841f970'),
       (16, NULL, 'przelewy24_type', '0');

INSERT INTO `sms_template_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywny', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywny ', 'disabled', '#A00', NULL, NULL);

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `is_verified`, `confirmed`, `actived`, `access`,
                    `date_register`, `date_activity`, `api_token`)
VALUES (1, 'admin@appcode.eu', '[\"ROLE_SUPER_ADMIN\"]', '$2y$13$y2/swJpgf3nDRCuCINXm4.841hO7gF0B1IE5Xz7a5tNH.L/UEQp26',
        1, NULL, 1, NULL, '2022-08-17 13:23:35', '2022-10-10 08:13:33', NULL);

INSERT INTO `user_data` (`id`, `user_id`, `photo_id`, `name`, `last_name`, `phone`, `street`, `postcode`, `city`, `nip`,
                         `company`, `regon`, `home_number`, `apartment_number`, `description`, `social_facebook`,
                         `social_twitter`, `social_instagram`, `social_linkedin`, `social_youtube`)
VALUES (1, 1, NULL, 'Programigo', ' Coders', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL);

INSERT INTO `user_email` (`id`, `user_id`, `sender_email`, `sender_label`, `sender_pass`, `sender_host`, `sender_port`,
                          `footer`)
VALUES (1, 1, 'dev@programigo.com', 'Programigo', 'Nk9hMEg1UXla', 'c1896.lh.pl', NULL, 'Podpis');

INSERT INTO `institutions_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywny', 'new', '#5A0', NULL, NULL),
       (2, 'Nieaktywny ', 'disabled', '#A00', NULL, NULL);

INSERT INTO `tasks_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Nowy', 'new', '#5A0', NULL, NULL),
       (2, 'W reazliacji ', 'inprogress', '#c48829', NULL, NULL),
       (3, 'Zakończony ', 'end', '#2976c4', NULL, NULL);

INSERT INTO `tickets_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Nowe', 'new', '#5A0', NULL, NULL);
INSERT INTO `files_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Nowe', 'new', '#5A0', NULL, NULL);
INSERT INTO `companies_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywna', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywna ', 'disabled', '#A00', NULL, NULL);
INSERT INTO `families_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywna', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywna ', 'disabled', '#A00', NULL, NULL);
INSERT INTO `babysitters_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywna', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywna ', 'disabled', '#A00', NULL, NULL);
INSERT INTO `patients_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywna', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywna ', 'disabled', '#A00', NULL, NULL);
INSERT INTO `transport_companies_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywna', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywna ', 'disabled', '#A00', NULL, NULL);
INSERT INTO `invoices_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywna', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywna ', 'disabled', '#A00', NULL, NULL);
INSERT INTO `contracts_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywna', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywna ', 'disabled', '#A00', NULL, NULL);
INSERT INTO `care_companies_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`)
VALUES (1, 'Aktywna', 'enabled', '#5A0', NULL, NULL),
       (2, 'Nieaktywna ', 'disabled', '#A00', NULL, NULL);

INSERT INTO `country` (`id`, `title`, `date_add`, `date_modify`)
VALUES (1, 'POLSKA', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (2, 'NIEMCY', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (3, 'UKRAINA', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (4, 'ROSJA', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (5, 'LITWA', '2022-12-01 00:00:00', '2022-12-01 00:00:00');

INSERT INTO `currencies` (`id`, `title`, `date_add`, `date_modify`)
VALUES (1, 'EUR', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (2, 'PLN', '2022-12-01 00:00:00', '2022-12-01 00:00:00');

INSERT INTO `language_levels` (`id`, `title`, `date_add`, `date_modify`)
VALUES (1, 'Podstawowy', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (2, 'Średniozaawansowany', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (3, 'Zaawansowany', '2022-12-01 00:00:00', '2022-12-01 00:00:00');

INSERT INTO `months` (`id`, `title`, `date_add`, `date_modify`)
VALUES (1, 'Styczeń', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (2, 'Luty', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (3, 'Marzec', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (4, 'Kwiecień', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (5, 'Maj', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (6, 'Czerwiec', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (7, 'Lipiec', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (8, 'Sierpień', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (9, 'Wrzesień', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (10, 'Październik', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (11, 'Listopad', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (12, 'Grudzień', '2022-12-01 00:00:00', '2022-12-01 00:00:00');

INSERT INTO `settlement_type` (`id`, `title`, `date_add`, `date_modify`)
VALUES (1, 'Suna Care', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (2, 'Secend lie care', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (3, 'DSB', '2022-12-01 00:00:00', '2022-12-01 00:00:00');

INSERT INTO `types_of_rates` (`id`, `title`, `date_add`, `date_modify`)
VALUES (1, 'Miesięczna', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (2, 'Roczna', '2022-12-01 00:00:00', '2022-12-01 00:00:00');

INSERT INTO `type_send_invoices` (`id`, `title`, `date_add`, `date_modify`)
VALUES (1, 'Poczta', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (2, 'Email', '2022-12-01 00:00:00', '2022-12-01 00:00:00'),
       (3, 'Poczta i Email', '2022-12-01 00:00:00', '2022-12-01 00:00:00');