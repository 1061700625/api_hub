CREATE TABLE IF NOT EXISTS admins (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS api_categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE,
  sort_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS apis (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  category_id INTEGER NULL,
  name TEXT NOT NULL,
  route TEXT NOT NULL UNIQUE,
  script_file TEXT NOT NULL,
  icon TEXT NULL,
  description TEXT NULL,
  method_set TEXT NOT NULL DEFAULT 'GET,POST',
  response_format TEXT NOT NULL DEFAULT 'JSON',
  access_level TEXT NOT NULL DEFAULT '免费',
  require_key INTEGER NOT NULL DEFAULT 0,
  status TEXT NOT NULL DEFAULT 'draft',
  call_count INTEGER DEFAULT 0,
  success_count INTEGER DEFAULT 0,
  avg_latency_ms INTEGER DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES api_categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS api_params (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  api_id INTEGER NOT NULL,
  param_name TEXT NOT NULL,
  required INTEGER NOT NULL DEFAULT 0,
  param_type TEXT NOT NULL DEFAULT 'string',
  description TEXT NULL,
  example_value TEXT NULL,
  sort_order INTEGER DEFAULT 0,
  FOREIGN KEY (api_id) REFERENCES apis(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS api_keys (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  uuid TEXT NOT NULL UNIQUE,
  email TEXT NOT NULL DEFAULT '',
  purpose TEXT NULL,
  status TEXT NOT NULL DEFAULT 'pending',
  ip TEXT NULL,
  user_agent TEXT NULL,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
  approved_at TEXT NULL
);

CREATE TABLE IF NOT EXISTS api_call_logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  api_id INTEGER NOT NULL,
  route TEXT NOT NULL,
  ip TEXT NULL,
  method TEXT NOT NULL,
  status_code INTEGER NOT NULL,
  latency_ms INTEGER NOT NULL,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (api_id) REFERENCES apis(id) ON DELETE CASCADE
);

CREATE TRIGGER IF NOT EXISTS trg_apis_updated_at
AFTER UPDATE ON apis
FOR EACH ROW
WHEN NEW.updated_at = OLD.updated_at
BEGIN
  UPDATE apis SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

INSERT OR IGNORE INTO api_categories(id, name, sort_order) VALUES
(1, '解析类', 1),
(2, '查询类', 2),
(3, '工具类', 3),
(4, '图片类', 4);

INSERT OR IGNORE INTO apis
(id, category_id, name, route, script_file, icon, description, method_set, response_format, access_level, status, call_count, success_count, avg_latency_ms)
VALUES
(1, 1, '网易云音乐SVIP解析', '163_music', '163_music.php', 'netease', '网易云音乐解析，演示接口会返回歌曲基础信息和播放地址。', 'GET,POST', 'JSON', '免费', 'published', 2133611, 2133611, 215),
(2, 2, 'IP归属地查询', 'ip_query', 'ip_query.php', 'ip', '查询IP地理位置及归属地信息，不传IP时默认使用当前访问IP。', 'GET,POST', 'JSON', '免费', 'published', 3100000, 3098800, 38),
(3, 3, 'JSON回显测试', 'echo_json', 'echo_json.php', 'json', '用于测试GET/POST参数是否正常进入API网关。', 'GET,POST', 'JSON', '免费', 'published', 12000, 12000, 9),
(4, 2, '城市天气id', 'weather_city_id', 'weather_city_id.php', 'weather', '查询演示城市对应的天气ID。', 'GET,POST', 'JSON', '免费', 'published', 1400, 1398, 42),
(5, 4, '豆包视频解析', 'doubao_video', 'doubao_video.php', 'doubao', '豆包短视频解析演示接口。', 'GET,POST', 'JSON', '免费', 'published', 2100, 2098, 63),
(6, 1, 'bilibili解析', 'bilibili', 'bilibili.php', 'bilibili', '只能解析bilibili短视频，画质为1080，支持合集演示。', 'GET,POST', 'JSON', '免费', 'published', 1700000, 1698123, 89),
(7, 1, '抖音无水印解析', 'douyin_no_watermark', 'douyin_no_watermark.php', 'douyin', '抖音去水印解析，支持图文和短视频解析。', 'GET,POST', 'JSON', '免费', 'published', 1600000, 1599012, 102),
(8, 3, '时间戳转换', 'timestamp', 'timestamp.php', 'clock', '时间戳与日期字符串互转。', 'GET,POST', 'JSON', '免费', 'published', 9800, 9780, 5);

INSERT OR IGNORE INTO api_params(api_id, param_name, required, param_type, description, example_value, sort_order) VALUES
(1, 'id', 0, 'string', '网易云音乐歌曲ID。id和url至少传一个。', '123456', 1),
(1, 'url', 0, 'string', '网易云音乐分享链接。id和url至少传一个。', 'https://music.163.com/song?id=123456', 2),
(2, 'ip', 0, 'string', '要查询的IP地址，不传则默认当前访问IP。', '8.8.8.8', 1),
(3, 'name', 0, 'string', '任意字符串，用于回显测试。', 'hello', 1),
(4, 'city', 1, 'string', '城市名称。', '北京', 1),
(5, 'url', 1, 'string', '豆包短视频链接。', 'https://example.com/video/1', 1),
(6, 'url', 1, 'string', 'bilibili视频链接。', 'https://www.bilibili.com/video/BVxxxx', 1),
(7, 'url', 1, 'string', '抖音分享链接。', 'https://v.douyin.com/xxxx/', 1),
(8, 'timestamp', 0, 'integer', 'Unix时间戳。', '1735689600', 1),
(8, 'datetime', 0, 'string', '日期时间字符串。', '2025-01-01 00:00:00', 2);

CREATE TABLE IF NOT EXISTS api_response_params (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  api_id INTEGER NOT NULL,
  param_name TEXT NOT NULL,
  param_type TEXT NOT NULL DEFAULT 'string',
  description TEXT NULL,
  example_value TEXT NULL,
  sort_order INTEGER DEFAULT 0,
  FOREIGN KEY (api_id) REFERENCES apis(id) ON DELETE CASCADE
);

INSERT OR IGNORE INTO api_response_params(api_id, param_name, param_type, description, example_value, sort_order) VALUES
(1, 'code', 'integer', '业务状态码，0 表示请求成功。', '0', 1),
(1, 'msg', 'string', '接口调用结果说明。', 'success', 2),
(1, 'data', 'object', '接口返回的业务数据。具体字段以对应 API 为准。', '{}', 3),
(2, 'code', 'integer', '业务状态码，0 表示请求成功。', '0', 1),
(2, 'msg', 'string', '接口调用结果说明。', 'success', 2),
(2, 'data', 'object', '接口返回的业务数据。具体字段以对应 API 为准。', '{}', 3),
(3, 'code', 'integer', '业务状态码，0 表示请求成功。', '0', 1),
(3, 'msg', 'string', '接口调用结果说明。', 'success', 2),
(3, 'data', 'object', '接口返回的业务数据。具体字段以对应 API 为准。', '{}', 3),
(4, 'code', 'integer', '业务状态码，0 表示请求成功。', '0', 1),
(4, 'msg', 'string', '接口调用结果说明。', 'success', 2),
(4, 'data', 'object', '接口返回的业务数据。具体字段以对应 API 为准。', '{}', 3),
(5, 'code', 'integer', '业务状态码，0 表示请求成功。', '0', 1),
(5, 'msg', 'string', '接口调用结果说明。', 'success', 2),
(5, 'data', 'object', '接口返回的业务数据。具体字段以对应 API 为准。', '{}', 3),
(6, 'code', 'integer', '业务状态码，0 表示请求成功。', '0', 1),
(6, 'msg', 'string', '接口调用结果说明。', 'success', 2),
(6, 'data', 'object', '接口返回的业务数据。具体字段以对应 API 为准。', '{}', 3),
(7, 'code', 'integer', '业务状态码，0 表示请求成功。', '0', 1),
(7, 'msg', 'string', '接口调用结果说明。', 'success', 2),
(7, 'data', 'object', '接口返回的业务数据。具体字段以对应 API 为准。', '{}', 3),
(8, 'code', 'integer', '业务状态码，0 表示请求成功。', '0', 1),
(8, 'msg', 'string', '接口调用结果说明。', 'success', 2),
(8, 'data', 'object', '接口返回的业务数据。具体字段以对应 API 为准。', '{}', 3);
