<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>ООО «ЦИТ «БиСофт» — Автоматизация технологических процессов</title>
    <style>
        :root {
            --primary: #0f4c81;
            --accent: #f5b400;
            --dark: #0b1d33;
            --muted: #6b7280;
            --light: #f5f7fb;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:"Figtree","Segoe UI",Arial,sans-serif;background:var(--light);color:var(--dark);line-height:1.6}
        a{text-decoration:none;color:inherit}
        img{max-width:100%;display:block}
        header{position:relative;overflow:hidden}
        .hero{
            min-height:90vh;
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
            gap:48px;
            align-items:center;
            padding:120px 7vw 80px;
            background:linear-gradient(135deg,rgba(15,76,129,0.9),rgba(11,29,51,0.95)),url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
            color:#fff;
        }
        .hero__badge{
            display:inline-flex;
            align-items:center;
            gap:10px;
            background:rgba(255,255,255,0.1);
            border:1px solid rgba(255,255,255,0.2);
            padding:10px 18px;
            border-radius:999px;
            font-size:14px;
            letter-spacing:0.05em;
            text-transform:uppercase;
        }
        .hero__subtitle{margin-top:18px;font-size:18px;color:rgba(255,255,255,0.8);max-width:520px}
        .hero__title{font-size:48px;font-weight:700;line-height:1.15;margin-top:24px;max-width:620px}
        .hero__cta{
            margin-top:36px;
            display:flex;
            flex-wrap:wrap;
            gap:16px;
        }
        .btn{
            padding:14px 28px;
            border-radius:999px;
            font-weight:600;
            transition:all .3s ease;
        }
        .btn--primary{background:var(--accent);color:#1a1a1a}
        .btn--primary:hover{transform:translateY(-3px)}
        .btn--outline{border:1px solid rgba(255,255,255,0.6);color:#fff}
        .btn--outline:hover{background:rgba(255,255,255,0.1)}
        .hero__stats{margin-top:54px;display:flex;flex-wrap:wrap;gap:32px}
        .hero__stat span{display:block;color:rgba(255,255,255,0.7);font-size:14px;margin-top:6px}
        nav{
            position:fixed;
            top:0;left:0;right:0;
            z-index:10;
            background:rgba(11,29,51,0.92);
            backdrop-filter:blur(14px);
            border-bottom:1px solid rgba(255,255,255,0.05);
        }
        .nav__inner{
            max-width:1180px;
            margin:0 auto;
            padding:18px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .nav__brand{font-size:18px;font-weight:700;color:#fff;display:flex;flex-direction:column;line-height:1.2}
        .nav__brand span{font-size:12px;font-weight:500;color:rgba(255,255,255,0.7);letter-spacing:0.08em;text-transform:uppercase}
        .nav__actions{display:flex;align-items:center;gap:16px}
        main section{padding:100px 7vw}
        .section__meta{text-transform:uppercase;font-size:14px;letter-spacing:0.08em;color:var(--accent);font-weight:600;margin-bottom:16px}
        .section__title{font-size:36px;font-weight:700;margin-bottom:24px;max-width:640px}
        .section__subtitle{color:var(--muted);max-width:680px;margin-bottom:48px}
        .grid{display:grid;gap:32px}
        .grid--3{grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
        .card{
            background:#fff;
            border-radius:20px;
            padding:32px;
            box-shadow:0 18px 40px rgba(15,76,129,0.08);
            transition:transform .3s ease, box-shadow .3s ease;
            height:100%;
            position:relative;
            overflow:hidden;
        }
        .card::after{
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(135deg,rgba(245,180,0,0.15),rgba(15,76,129,0.15));
            opacity:0;
            transition:opacity .3s ease;
        }
        .card:hover{transform:translateY(-8px);box-shadow:0 25px 55px rgba(15,76,129,0.12)}
        .card:hover::after{opacity:1}
        .card > *{position:relative;z-index:1}
        .card__icon{
            width:56px;height:56px;
            border-radius:16px;
            background:rgba(15,76,129,0.12);
            display:flex;align-items:center;justify-content:center;
            font-size:26px;color:var(--primary);margin-bottom:20px;
        }
        .card h3{font-size:20px;margin-bottom:12px}
        .card p{color:var(--muted);font-size:15px}
        .image-split{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
            gap:40px;
            align-items:center;
        }
        .image-split__media{
            position:relative;
            border-radius:24px;
            overflow:hidden;
            box-shadow:0 24px 50px rgba(15,76,129,0.15);
        }
        .image-split__media img{object-fit:cover;height:100%}
        .image-split__badge{
            position:absolute;
            bottom:24px;
            left:24px;
            background:rgba(15,76,129,0.9);
            color:#fff;
            border-radius:18px;
            padding:16px 22px;
            box-shadow:0 14px 30px rgba(11,29,51,0.25);
        }
        .feature-list{display:grid;gap:18px;margin-top:24px}
        .feature-list li{
            display:flex;gap:12px;align-items:flex-start;
            color:var(--muted);
        }
        .feature-list svg{width:18px;height:18px;color:var(--accent);margin-top:4px;flex-shrink:0}
        .cta{
            background:linear-gradient(135deg,rgba(15,76,129,0.95),rgba(11,29,51,0.95));
            color:#fff;
            text-align:center;
            padding:120px 7vw;
            position:relative;
            overflow:hidden;
        }
        .cta::after{
            content:'';
            position:absolute;
            inset:0;
            background:url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1600&q=60') center/cover;
            mix-blend-mode:soft-light;
            opacity:0.25;
        }
        .cta > *{position:relative;z-index:1}
        .cta__title{font-size:40px;font-weight:700;margin-bottom:16px}
        .cta__subtitle{color:rgba(255,255,255,0.8);margin-bottom:36px}
        footer{
            background:var(--dark);
            color:rgba(255,255,255,0.8);
            padding:40px 7vw;
        }
        footer .footer__inner{display:flex;flex-direction:column;gap:16px;text-align:center;font-size:14px}
        @media (max-width:768px){
            .hero{padding:140px 7vw 80px}
            .hero__title{font-size:36px}
            .nav__actions{display:none}
            .section__title{font-size:28px}
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav__inner">
            <div class="nav__brand">
                ООО «ЦИТ «БиСофт»
                <span>Автоматизация и телемеханизация</span>
            </div>
            <div class="nav__actions">
                <a href="{{ route('tests.index') }}" class="btn btn--primary" style="background:transparent;border:1px solid rgba(255,255,255,0.6);color:#fff">Пройти тесты</a>
                <a href="#services" class="btn btn--outline">Услуги</a>
                <a href="#contact" class="btn btn--primary">Связаться</a>
            </div>
        </div>
    </nav>

    <header>
        <div class="hero">
            <div>
                <div class="hero__badge">
                    <span>Полный цикл автоматизации</span>
                </div>
                <h1 class="hero__title">Телемеханизация объектов нефтедобычи и нефтеподготовки</h1>
                <p class="hero__subtitle">
                    ООО «ЦИТ «БиСофт» обеспечивает комплексную автоматизацию скважин, кустовых площадок и установок подготовки нефти:
                    проектируем, собираем, программируем и поддерживаем инфраструктуру АСУ ТП на всех этапах жизненного цикла.
                </p>
                <div class="hero__cta">
                    <a href="{{ route('tests.index') }}" class="btn btn--primary">Перейти к тестам</a>
                    <a href="#contact" class="btn btn--primary">Запросить консультацию</a>
                    <a href="#about" class="btn btn--outline">Подробнее о компании</a>
                </div>
                <div class="hero__stats">
                    <div class="hero__stat">
                        <strong style="font-size:32px">18+</strong>
                        <span>лет проектов по автоматизации нефтегазовых объектов</span>
                    </div>
                    <div class="hero__stat">
                        <strong style="font-size:32px">1000+</strong>
                        <span>станций управления и шкафов телемеханизации в эксплуатации</span>
                    </div>
                </div>
            </div>
            <div class="image-split__media" style="height:100%;min-height:420px">
                <!-- IMAGE PLACEHOLDER: современная диспетчерская или SCADA центр -->
                <img src="/images/landing-bg1.jpg" alt="Щиты телемеханизации и инженер АСУ ТП">
                <div class="image-split__badge">
                    <strong>Цифровой контроль</strong><br>
                    Снижаем аварийность, повышаем добычу, держим объекты под контролем в единой SCADA.
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="about" class="image-split">
            <div>
                <div class="section__meta">о компании</div>
                <h2 class="section__title">ООО «ЦИТ «БиСофт» — автоматизация добычи и подготовки нефти с 2008 года</h2>
                <p class="section__subtitle">
                    Компания входит в контур УК «ШЕШМАОЙЛ» и специализируется на автоматизации и телемеханизации объектов добычи и подготовки нефти.
                    Повышаем эффективность производства, внедряем строгий контроль технологических операций и оптимизируем затраты заказчиков.[https://sheshmaoil.ru/kompanii/ooo-tsit-bisoft/](https://sheshmaoil.ru/kompanii/ooo-tsit-bisoft/)
                </p>
                <ul class="feature-list">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none"><path d="m5 12 4.196 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Организуем работы по автоматизации и телемеханизации для добывающих и сервисных активов группы.
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none"><path d="m5 12 4.196 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Руководствуемся принципами повышения эффективности, качества услуг и точного соблюдения технологических регламентов.
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none"><path d="m5 12 4.196 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Обеспечиваем полный цикл — от разработки конструкторской документации до внедрения и сервисного сопровождения.
                    </li>
                </ul>
            </div>
            <div class="image-split__media">
                <!-- IMAGE PLACEHOLDER: производственный участок или сборочный цех шкафов -->
                <img src="/images/landing-bg1.jpg" alt="Инженеры АСУ ТП за работой">
            </div>
        </section>

        <section id="services">
            <div class="section__meta">наши компетенции</div>
            <h2 class="section__title">Комплексные решения для телемеханизации и АСУ ТП нефтедобычи</h2>
            <p class="section__subtitle">
                Соединяем технологические объекты, контроллеры и верхний уровень, обеспечивая прозрачную добычу и быструю реакцию на отклонения.
                Системы легко масштабируются и интегрируются с цифровыми платформами заказчика.
            </p>
            <div class="grid grid--3">
                <div class="card">
                    <div class="card__icon">⚙️</div>
                    <h3>Модульная телемеханизация фонда скважин</h3>
                    <p>
                        Проектируем и внедряем модульные системы телемеханизации с возможностью масштабирования, используя беспроводные сети Zigbee и Wi‑Fi для оптимизации инфраструктуры.[https://sheshmaoil.ru/kompanii/ooo-tsit-bisoft/](https://sheshmaoil.ru/kompanii/ooo-tsit-bisoft/)
                    </p>
                </div>
                <div class="card">
                    <div class="card__icon">🛰️</div>
                    <h3>Шкафы телемеханики и управления</h3>
                    <p>
                        Разрабатываем конструкторскую документацию и собираем шкафы телемеханики, станции управления, шкафы АСУ ТП и панели для инновационных проектов и решений.
                    </p>
                </div>
                <div class="card">
                    <div class="card__icon">💻</div>
                    <h3>Программирование ПЛК и SCADA</h3>
                    <p>
                        Разрабатываем и отлаживаем ПО для контроллеров АСУ ТП, создаём мнемосхемы, отчётные формы и алгоритмы для SCADA, интегрированной с КРОН-ТМ.
                    </p>
                </div>
                <div class="card">
                    <div class="card__icon">🔌</div>
                    <h3>Строительно-монтажные и пусконаладочные работы</h3>
                    <p>Выполняем монтаж шкафов, подключение датчиков и каналов связи на объекте, проводим пуско-наладочные работы.</p>
                </div>
                <div class="card">
                    <div class="card__icon">🔄</div>
                    <h3>Модернизация и сервис</h3>
                    <p>
                        Обновляем устаревшие системы управления, выполняем миграцию на современные контроллеры, обеспечиваем круглосуточную поддержку и SLA.
                    </p>
                </div>
                <div class="card">
                    <div class="card__icon">📊</div>
                    <h3>Измерение параметров добычи</h3>
                    <p>Проводим измерение газового фактора и вязкости продукции мобильной установкой БИПС — до 700 скважин в год.</p>
                </div>
            </div>
        </section>

        <section style="background:#fff">
            <div class="section__meta">как мы работаем</div>
            <h2 class="section__title">От обследования до ввода в промышленную эксплуатацию</h2>
            <div class="grid grid--3">
                <div class="card">
                    <div class="card__icon">1</div>
                    <h3>Диагностика и проект</h3>
                    <p>Проводим аудит текущей инфраструктуры, подготавливаем конструкторскую документацию и согласуем дорожную карту внедрения.</p>
                </div>
                <div class="card">
                    <div class="card__icon">2</div>
                    <h3>Инжиниринг и сборка</h3>
                    <p>Собираем шкафы, программируем контроллеры, тестируем алгоритмы, каналы связи и интеграцию с верхним уровнем.</p>
                </div>
                <div class="card">
                    <div class="card__icon">3</div>
                    <h3>Внедрение и поддержка</h3>
                    <p>Проводим пусконаладочные работы, обучаем персонал, берем системы на сопровождение и мониторинг 24/7.</p>
                </div>
            </div>
        </section>

        <section style="background:var(--light)">
            <div class="section__meta">наши технологии</div>
            <h2 class="section__title">Инновации, которые поддерживают добычу</h2>
            <div class="grid grid--3">
                <div class="card">
                    <div class="card__icon">📡</div>
                    <h3>Беспроводная телемеханика</h3>
                    <p>Используем сети Zigbee и Wi‑Fi для сбора информации со скважин, снижая затраты на кабельную инфраструктуру и ускоряя внедрение решений.[https://sheshmaoil.ru/kompanii/ooo-tsit-bisoft/](https://sheshmaoil.ru/kompanii/ooo-tsit-bisoft/)</p>
                </div>
                <div class="card">
                    <div class="card__icon">🧠</div>
                    <h3>Интеграция с КРОН-ТМ</h3>
                    <p>Связываем телемеханику со специализированным ПО КРОН-ТМ, обеспечивая полный контроль добывающего фонда и оперативную аналитику.</p>
                </div>
                <div class="card">
                    <div class="card__icon">🧪</div>
                    <h3>Диагностика оборудования</h3>
                    <p>С помощью мобильной установки БИПС измеряем газовый фактор и вязкость продукции, формируя годовой план на ~700 скважин.</p>
                </div>
            </div>
        </section>

        <section style="background:#fff">
            <div class="section__meta">наши достижения</div>
            <h2 class="section__title">Результаты, подтвержденные практикой</h2>
            <div class="grid grid--3">
                <div class="card">
                    <div class="card__icon">🏭</div>
                    <h3>Комплексная автоматизация</h3>
                    <p>Освоена автоматизация объектов нефтедобычи и нефтесбора, включая сборку широкой номенклатуры шкафов автоматизации.</p>
                </div>
                <div class="card">
                    <div class="card__icon">🚀</div>
                    <h3>Проекты КОГС‑1М</h3>
                    <p>Разработана и внедрена система автоматизации комплекса откачки газа из скважин КОГС‑1М в активе УК «ШЕШМАОЙЛ».</p>
                </div>
                <div class="card">
                    <div class="card__icon">🤝</div>
                    <h3>Надёжные заказчики</h3>
                    <p>С нами работают добывающие компании УК «ШЕШМАОЙЛ» и малые нефтяные компании АО «Консалтинговый центр».</p>
                </div>
            </div>
        </section>

        <section id="contact" class="cta">
            <div class="section__meta" style="color:var(--accent);letter-spacing:0.2em;margin-bottom:12px">свяжитесь с нами</div>
            <h2 class="cta__title">Готовы ускорить цифровую трансформацию добычи</h2>
            <p class="cta__subtitle">
                Расскажите о вашем объекте — предложим архитектуру автоматизации и телемеханизации, подберём оборудование и подготовим дорожную карту внедрения.
                <br><br>
                Тел.: +7 (8553) 39-39-17 &nbsp;•&nbsp; usmaev_am@shoil.ru<br>
            </p>
            <div class="hero__cta" style="justify-content:center;margin-top:24px">
                <a href="{{ route('tests.index') }}" class="btn btn--outline" style="border-color:rgba(255,255,255,0.8);color:#fff">Пройти тесты</a>
                <a href="mailto:usmaev_am@shoil.ru" class="btn btn--primary" style="background:#fff;color:var(--dark)">Отправить запрос</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer__inner">
            <strong>ООО «ЦИТ «БиСофт»</strong>
            <span>Комплексная автоматизация технологических процессов, телемеханизация скважин, шкафы управления, SCADA.</span>
            <span>© {{ date('Y') }} ООО «ЦИТ «БиСофт». Все права защищены.</span>
        </div>
    </footer>
</body>
</html>

