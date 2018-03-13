--
-- Created by IntelliJ IDEA.
-- User: jw
-- Date: 2018/3/12 0012
-- Time: 17:34
-- To change this template use File | Settings | File Templates.
--

local module = {}

module.nonces = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ'
module.nonceLength = 57

-- 获取客户端IP
function module.getClientIp()
    local ip = ngx.var.remote_addr
    if ip == nil then
        ip = 'unknown'
    end

    return ip
end

-- 输出日志
function module.log(file, msg)
    local fd = io.open(file, 'ab')
    if fd == nil then
        return
    end
    fd:write(msg)
    fd:flush()
    fd:close()
end

-- 生成随机整数
function module.random(min, max)
    math.randomseed(os.clock() * math.random(1000000, 9000000) + math.random(1000000, 9000000))
    return math.random(min, max)
end

-- 生成随机字符串
function module.createNonceStr(length)
    if type(length) == 'number' and length >= 1 then
        local chars = {}
        for i=1,length,1 do
            local index = module.random(1, module.nonceLength)
            chars[i] = string.sub(module.nonces, index, index)
        end
        return table.concat(chars, '')
    else
        return nil
    end
end

return module