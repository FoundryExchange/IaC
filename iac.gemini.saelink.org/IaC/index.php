<?php
include 'db.php'; // 引入数据库连接

// 从URL参数获取关键词
$code_keyword = isset($_GET['code']) ? $_GET['code'] : '';

if ($code_keyword) {
    try {
        // 准备SQL查询，检索包含关键词的最新记录
        $sql = "SELECT Code_Content FROM Ragdoll_IaC_Code 
                WHERE Code_Content LIKE CONCAT('%', ?, '%') 
                ORDER BY Code_Created_At DESC 
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $code_keyword, PDO::PARAM_STR);
        $stmt->execute();

        // 获取查询结果
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            // 转换换行符从 \r\n 到 \n
            $code_content = str_replace("\r\n", "\n", $result['Code_Content']);
            echo $code_content;
        } else {
            echo "No records found containing the keyword: $code_keyword";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No code keyword provided.";
}
?>
