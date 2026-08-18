--// Infinity Jump (Balenciaga UI 連動版)
local UIS = game:GetService("UserInputService")
local Players = game:GetService("Players")
local LP = Players.LocalPlayer

-- UI の State を使う
-- State.infJumpEnabled  → ON/OFF
-- State.infJumpMode     → "hold" / "toggle"
local toggleState = false

local function doJump()
    local char = LP.Character
    local hum = char and char:FindFirstChildOfClass("Humanoid")
    if hum then
        hum:ChangeState(Enum.HumanoidStateType.Jumping)
    end
end

-- HOLD モード
UIS.InputBegan:Connect(function(input)
    if not State.infJumpEnabled then return end
    if State.infJumpMode ~= "hold" then return end

    if input.KeyCode == Enum.KeyCode.Space then
        while UIS:IsKeyDown(Enum.KeyCode.Space) and State.infJumpEnabled and State.infJumpMode == "hold" do
            doJump()
            task.wait(0.05)
        end
    end
end)

-- TOGGLE モード
UIS.InputBegan:Connect(function(input)
    if not State.infJumpEnabled then return end
    if State.infJumpMode ~= "toggle" then return end

    if input.KeyCode == Enum.KeyCode.Space then
        toggleState = not toggleState
        if toggleState then
            task.spawn(function()
                while toggleState and State.infJumpEnabled and State
